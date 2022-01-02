export default {
    data: function() {
        return {
            userAuthenticated: false,
            user_info: {}
        };
    },
    created: function() {
        this.checkAuth2();
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
                vm.user_info = response.data.data;
                vm.userAuthenticated = true;		
		return vm.user_info;
            }
	    return;
        }
    }
};
