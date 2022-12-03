window._ = require("lodash");

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

axios.interceptors.request.use(function (config) {
    if (window.localStorage.getItem("token")) {
        token = window.localStorage.getItem("token");
        config.headers.Authorization = `Bearer ${token}`;
    } else {
        delete config.headers.Authorization;
    }
    window.axios.defaults.headers.common["Accept"] = "application/json";
    return config;
});

window.axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        return error.response;
    }
);
