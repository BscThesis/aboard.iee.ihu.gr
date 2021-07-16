import { bus } from "../../app";
import { toast } from "bulma-toast";

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
        checkAuth2: function () {
            console.log("1");
            const response = axios.get("/api/auth/user");
            console.log("2");
            console.log(response);
            console.log("3");
            if (response.status == 200 && response.statusText == "OK") {
                console.log("4");
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
                console.log("5");
                const response = axios.post("/api/auth/refresh", {
                    refresh_token: localStorage.getItem("refresh"),
                });
                console.log(response);
                // axios
                //     .post("/api/auth/refresh", {
                //         refresh_token: localStorage.getItem("refresh"),
                //     })
                //     .then(function (response) {
                //         if (response.status == 200) {
                //             localStorage.token = response.data.access_token;
                //             localStorage.refresh =
                //                 response.data.refresh_token;
                //             axios
                //                 .get("/api/auth/user")
                //                 .then((response) => {
                //                     localStorage.setItem(
                //                         "user_info",
                //                         JSON.stringify(response.data.data)
                //                     );
                //                 })
                //                 .catch((error) => {
                //                     localStorage.removeItem("token");
                //                     localStorage.removeItem("refresh");
                //                     localStorage.removeItem("user_info");
                //                     delete axios.defaults.headers.common[
                //                         "Authorization"
                //                     ];
                //                     window.location.href = "/login";
                //                 });
                //         } else {
                //             console.log(response.data);
                //         }
                //     })
                //     .catch(function (error) {
                //         console.log(error);
                //     });
            } else {
                console.log("6");
                localStorage.removeItem("token");
                localStorage.removeItem("refresh");
                localStorage.removeItem("user_info");
                delete axios.defaults.headers.common["Authorization"];
            }
        },
    },
};
