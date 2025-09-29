import storage from "./storage";
import request from "./request";

class User {
    
    constructor() {
        this.user = storage.me ? storage.me : null;

        storage.onChange((key) => {
            if (key === 'me') {
                this.user = storage.me ? storage.me : null;
            }
        })
    }

    async getPrivileges() {
        return new Promise( (resolve, reject) => {
            request.get('whoami').then(response => {
                if (response.data) {
                    this.user = {
                        ...(this.user || {}),
                        ...response.data,
                        is_admin: Boolean(response.data.is_admin),
                        is_author: Boolean(response.data.is_author)
                    }
                    resolve({
                        is_admin: Boolean(response.data.is_admin),
                        is_author: Boolean(response.data.is_author)
                    })
                }
            })
        })
    }
}

const user = new User()

export default user;
