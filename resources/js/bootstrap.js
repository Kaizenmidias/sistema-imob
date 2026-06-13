import axios from 'axios';

window.axios = axios;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
if (csrfToken) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

window.getCsrfToken = () => csrfToken;
window.getCookieValue = (name) => {
  const encodedName = `${name}=`;
  const parts = document.cookie.split(';').map((item) => item.trim());
  const match = parts.find((part) => part.startsWith(encodedName));
  if (!match) {
    return '';
  }

  return decodeURIComponent(match.slice(encodedName.length));
};
