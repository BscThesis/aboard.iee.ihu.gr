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
                const response = await axios.get("/api/auth/user");
                if (response.status == 200 && response.statusText == "OK") {
                    localStorage.setItem(
                        "user_info",
                        JSON.stringify(response.data.data)
                    );
                    vm.userAuthenticated = true;
                } else if (
                    response.status == 401 &&
                    response.statusText == "Unauthorized"
                ) {
                    axios
                        .post("/api/auth/refresh", {
                            refresh_token: localStorage.getItem("refresh"),
                        })
                        .then(function (response) {
                            if (response.status == 200) {
                                localStorage.token = response.data.access_token;
                                localStorage.refresh =
                                    response.data.refresh_token;
                                axios
                                    .get("/api/auth/user")
                                    .then((response) => {
                                        localStorage.setItem(
                                            "user_info",
                                            JSON.stringify(response.data.data)
                                        );
                                    })
                                    .catch((error) => {
                                        localStorage.removeItem("token");
                                        localStorage.removeItem("refresh");
                                        localStorage.removeItem("user_info");
                                        delete axios.defaults.headers.common[
                                            "Authorization"
                                        ];
                                        window.location.href = "/login";
                                    });
                                window.location.replace("/");
                            } else {
                                console.log(response.data);
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                } else {
                    localStorage.removeItem("token");
                    localStorage.removeItem("refresh");
                    localStorage.removeItem("user_info");
                    delete axios.defaults.headers.common["Authorization"];
                }
            }
            bus.$emit("authCheckFinished");
        },
    },
};
