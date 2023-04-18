const config = {
  basename: process.env.REACT_APP_BASEPATH,
  api_url: process.env.REACT_APP_API_URL || 'http://127.0.0.1:8000/api/v2/',
  site_title: process.env.SITE_TITLE || 'Τμήμα Μηχανικών Πληροφορικής και Ηλεκτρονικών Συστημάτων',
  site_title_en: process.env.SITE_TITLE_EN || 'Department of Information Technology and Electronic Systems Engineering',
};
window.config = config;

export default config;