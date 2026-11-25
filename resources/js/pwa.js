const manifest = document.querySelector('link[rel="manifest"]');
const panels = [...document.querySelectorAll('[data-pwa-install]')];
const standalone = window.matchMedia('(display-mode: standalone)');
const isInstalled = () => standalone.matches || window.navigator.standalone === true;
const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
let installPrompt = null;
let dismissed = false;
try { dismissed = sessionStorage.getItem('si-kb-rs-install-dismissed') === '1'; } catch {}

function renderInstallPanel() {
  const visible = !isInstalled() && !dismissed && (Boolean(installPrompt) || isIOS);
  panels.forEach(panel => {
    panel.hidden = !visible;
    panel.querySelector('[data-pwa-install-button]').hidden = !installPrompt;
    panel.querySelector('[data-pwa-ios-help]').hidden = !isIOS || Boolean(installPrompt);
  });
}

window.addEventListener('beforeinstallprompt', event => {
  if (isIOS) return;
  event.preventDefault();
  installPrompt = event;
  renderInstallPanel();
});
window.addEventListener('appinstalled', () => {
  installPrompt = null;
  panels.forEach(panel => { panel.hidden = true; });
});
standalone.addEventListener('change', renderInstallPanel);

panels.forEach(panel => {
  panel.querySelector('[data-pwa-dismiss]').addEventListener('click', () => {
    dismissed = true;
    try { sessionStorage.setItem('si-kb-rs-install-dismissed', '1'); } catch {}
    renderInstallPanel();
  });
  panel.querySelector('[data-pwa-install-button]').addEventListener('click', async event => {
    if (!installPrompt) return;
    const prompt = installPrompt;
    const button = event.currentTarget;
    button.disabled = true;
    try {
      await prompt.prompt();
      await prompt.userChoice;
      installPrompt = null;
    } catch {
      installPrompt = null;
    } finally {
      button.disabled = false;
      renderInstallPanel();
    }
  });
});
renderInstallPanel();

if (manifest && window.isSecureContext && 'serviceWorker' in navigator) {
  const register = () => {
    const worker = new URL('sw.js', manifest.href);
    const scope = new URL('./', manifest.href).pathname;
    navigator.serviceWorker.register(worker.href, { scope, updateViaCache: 'none' }).catch(() => {
      // Installation is optional; authentication and online work remain available.
    });
  };
  if (document.readyState === 'complete') register();
  else window.addEventListener('load', register, { once: true });
}
