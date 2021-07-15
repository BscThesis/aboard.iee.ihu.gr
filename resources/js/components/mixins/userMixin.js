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
                    console.log("Unauthorized from here");
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
