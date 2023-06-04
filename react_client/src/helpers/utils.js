import config from "../config"

class Utils {
    redirectToApiSSO = async () => {
   
    
        const rand = Math.random() * 10000
        const login_url = `${config.api_url}auth/login_web?c=${rand}&redirect=${encodeURIComponent(config.redirect)}`
    
        window.location.href = `${config.api_url}auth/login_web?c=${rand}&redirect=${encodeURIComponent(config.redirect)}`
        return;
        
    }
}


export default new Utils