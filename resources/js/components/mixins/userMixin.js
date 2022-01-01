export default {
    data: function() {
        return {
            userAuthenticated: false,
            user_info: {}
        };
    },
    created: function() {
        this.checkAuth();
    },
    methods: {
        checkAuth: async function() {
            let vm = this;
            //if (this.$cookies.get("token")) {
            const response = await vm.checkAuth2();
            //}
        },
        checkAuth2: async function() {
            let vm = this;
            const response = await axios.get("/api/auth/user");
            if (
                response.data.data != null &&
                response.status == 200 &&
                response.statusText == "OK"
            ) {
                vm.userInfo = response.data.data;
                vm.userAuthenticated = true;
            }
        }
    }
};
