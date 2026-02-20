// ======= SIMPLE PODCAST CAROUSEL =======
// Add your episode links here:
const EPISODES = [
    "https://open.spotify.com/episode/7gXsD7WvGL2vgaL4DcCV73?si=NeyF-brrTg6Su8SG6d32Nw",
    "https://open.spotify.com/episode/79CaqQBI5lf2UDAgos8sAh?si=BOUCFLcBT7Kc4Qcvuzf2Xg",
    "https://open.spotify.com/episode/6Do6mlndjDcwQgcDu6338h?si=ArvOZT2ETiGWQXH3EigmXg",
    "https://open.spotify.com/episode/1JB9ivsNimSci1cSzpYw63?si=vN6WhO3LR-q6alwHP6GvKg",
    "https://open.spotify.com/episode/3V9gpnBYqOsoGalte7OJJc?si=6JSp_0egRYe-6DlgQoawcw"
];

// ======= DO NOT EDIT BELOW THIS LINE =======

const carousel = document.getElementById("podcastCarousel");

function convertToEmbed(url) {
    const id = url.split("/episode/")[1].split("?")[0];
    return `https://open.spotify.com/embed/episode/${id}?theme=0`;
}

function loadEpisodes() {
    EPISODES.forEach((url, i) => {
        const embed = convertToEmbed(url);

        const card = document.createElement("div");
        card.className = "podcast-card";

        card.innerHTML = `
            <iframe style="border-radius:12px" src="${embed}" width="100%" height="232" frameborder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
            <h3 class="podcast-title">Episode ${i + 1}</h3>
        `;

        carousel.appendChild(card);
    });
}

// ======= ARROWS =======
document.querySelector(".podcast-arrow.next").onclick = () => {
    carousel.scrollLeft += 400;
};
document.querySelector(".podcast-arrow.prev").onclick = () => {
    carousel.scrollLeft -= 400;
};

// ======= AUTO SLIDE =======
setInterval(() => {
    carousel.scrollLeft += 400;
}, 4500);

// ======= INIT =======
loadEpisodes();