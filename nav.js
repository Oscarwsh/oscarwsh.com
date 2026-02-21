(function(){
  const nav = document.querySelector('nav.site-nav');
  const btn = nav && nav.querySelector('.nav-toggle');
  if (!nav || !btn) return;

  const toggle = () => {
    const isOpen = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(isOpen));
  };

  btn.addEventListener('click', toggle);

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      nav.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    }
  });
})();