const config = {
  basename: process.env.REACT_APP_BASEPATH,
  api_url: process.env.REACT_APP_API_URL || 'http://127.0.0.1:8000/api/v2/',
  site_title: process.env.REACT_APP_SITE_TITLE || 'test',
  site_title_en: process.env.REACT_APP_SITE_TITLE_EN || 'test',
  redirect: process.env.REACT_REDIRECT_URL || 'https://aboard.iee.ihu.gr',
};
window.config = config;

export default config;