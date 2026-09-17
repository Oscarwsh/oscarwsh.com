<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$wcaId = '2025CEDE03';
$apiBase = 'https://www.worldcubeassociation.org/api/v0/persons/' . rawurlencode($wcaId);

function fetchWcaJson(string $url): array
{
	$curl = curl_init($url);
	curl_setopt_array($curl, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT => 10,
		CURLOPT_USERAGENT => 'Oscarwsh.com personal records',
	]);

	$response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($response === false || $status < 200 || $status >= 300) {
		throw new RuntimeException('The WCA API request failed.');
	}

	$data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
	return is_array($data) ? $data : [];
}

function formatResult(?int $value, string $eventId): string
{
	if ($value === null || $value <= 0) {
		return 'N/A';
	}

	if ($eventId === '333fm') {
		return (string) $value;
	}

	if ($value >= 60000) {
		$minutes = intdiv($value, 60000);
		$seconds = ($value % 60000) / 1000;
		return sprintf('%d:%05.2f', $minutes, $seconds);
	}

	return number_format($value / 1000, 2, '.', '') . 's';
}

function bestResult(array $results, string $eventId, string $type): ?array
{
	$best = null;

	foreach ($results as $result) {
		if (($result['event_id'] ?? '') !== $eventId) {
			continue;
		}

		$value = (int) ($result[$type] ?? -1);
		if ($value <= 0 || ($best !== null && $value >= $best[$type])) {
			continue;
		}

		$best = $result;
	}

	return $best;
}

try {
	$results = fetchWcaJson($apiBase . '/results');
	$competitions = fetchWcaJson($apiBase . '/competitions');
	$competitionById = [];

	foreach ($competitions as $competition) {
		if (isset($competition['id'])) {
			$competitionById[$competition['id']] = $competition;
		}
	}

	$events = [
		'222' => '2x2',
		'333' => '3x3',
		'444' => '4x4',
		'555' => '5x5',
		'666' => '6x6',
		'777' => '7x7',
		'333bf' => '3BLD',
		'333fm' => 'FMC',
		'333oh' => '3OH',
		'clock' => 'Clock',
		'minx' => 'Mega',
		'pyram' => 'Pyra',
		'skewb' => 'Skewb',
		'sq1' => 'SQ-1',
		'444bf' => '4BLD',
		'555bf' => '5BLD',
		'333mbf' => 'MBLD',
	];

	$rows = [];
	foreach ($events as $eventId => $eventName) {
		$single = bestResult($results, $eventId, 'best');
		$average = bestResult($results, $eventId, 'average');
		$singleCompetition = $competitionById[$single['competition_id'] ?? ''] ?? null;
		$averageCompetition = $competitionById[$average['competition_id'] ?? ''] ?? null;

		$rows[] = [
			'event' => $eventName,
			'single' => formatResult($single['best'] ?? null, $eventId),
			'singleDate' => $singleCompetition['start_date'] ?? 'N/A',
			'singleCompetition' => $singleCompetition['name'] ?? 'N/A',
			'singleUrl' => $singleCompetition['url'] ?? 'https://www.worldcubeassociation.org/competitions/',
			'average' => formatResult($average['average'] ?? null, $eventId),
			'averageDate' => $averageCompetition['start_date'] ?? 'N/A',
			'averageCompetition' => $averageCompetition['name'] ?? 'N/A',
			'averageUrl' => $averageCompetition['url'] ?? 'https://www.worldcubeassociation.org/competitions/',
		];
	}

	echo json_encode(['wcaId' => $wcaId, 'records' => $rows], JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
	http_response_code(502);
	echo json_encode(['error' => 'Personal records are temporarily unavailable.']);
}

