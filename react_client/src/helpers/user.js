import storage from "./storage";
import request from "./request";

class User {
    
    constructor() {
        this.user = storage.me ? storage.me : {};

        storage.onChange((key) => {
            if (key === 'me')
                this.user = storage.me ? storage.me : {};
        })
    }

    async getPrivileges() {
        return new Promise( (resolve, reject) => {
            request.get('whoami').then(response => {
                if (response.data) {
                    resolve({
                        is_admin: response.data.is_admin,
                        is_author: response.data.is_author
                    })
                }
            })
        })
    }
}

const user = new User()

export default user;