import { bus } from "../../app";

export default {
    data: function () {
        return {
            userAuthenticated: false,
        };
    },
    created: function () {
        this.checkAuth();
    },
    methods: {
        checkAuth: async function () {
            let vm = this;
            if (localStorage.getItem("token")) {
                const response = await vm.checkAuth2();
            }
            bus.$emit("authCheckFinished");
        },
        checkAuth2: async function () {
            let vm = this;
            const response = await axios.get("/api/auth/user");
            if (response.status == 200 && response.statusText == "OK") {
                localStorage.setItem(
                    "user_info",
                    JSON.stringify(response.data.data)
                );
                vm.userAuthenticated = true;
            } else if (
                response.status == 401 &&
                response.statusText == "Unauthorized" &&
                localStorage.getItem("refresh")
            ) {
                const response = await axios.post("/api/auth/refresh", {
                    refresh_token: localStorage.getItem("refresh"),
                });
                if (response.statusText == "OK" && response.status == 200) {
                    localStorage.token = response.data.access_token;
                    localStorage.refresh = response.data.refresh_token;
                    const response2 = await axios.get("/api/auth/user");
                    localStorage.setItem(
                        "user_info",
                        JSON.stringify(response2.data.data)
                    );
                    vm.userAuthenticated = true;
                } else {
                    localStorage.removeItem("token");
                    localStorage.removeItem("refresh");
                    localStorage.removeItem("user_info");
                    delete axios.defaults.headers.common["Authorization"];
                    window.location.href = "/login";
                }
            } else {
                localStorage.removeItem("token");
                localStorage.removeItem("refresh");
                localStorage.removeItem("user_info");
                delete axios.defaults.headers.common["Authorization"];
                window.location.href = "/login";
            }
        },
    },
};
