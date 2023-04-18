import axios from 'axios';
import config from '../config';
import storage from './storage';

const baseURL = config.api_url;

class Request {

  
  constructor() {
    this.axiosInstance = axios.create({
      baseURL: baseURL,
      // withCredentials: true,
    });
    this.request_instances = [];

    this.bearer = storage.token !== null && storage.token != 'null' ? storage.token : null;

    this.unsetStorage = storage.onChange(() => {
      this.bearer = storage.token !== null && storage.token != 'null' ? storage.token : null;
    })
  }

  createHttpRequest(method, url, data = null, single = true, multipart = false) {
    const methodToLowerCase = method.toLowerCase();
    const source = axios.CancelToken.source();
    const cancelToken = source.token;
    if (methodToLowerCase in axios && typeof axios[methodToLowerCase] === 'function') {
      this.axiosInstance = axios.create({
        baseURL: baseURL,
        // withCredentials: true,
        cancelToken: cancelToken
      });

        this.axiosInstance.interceptors.request.use( (config) => {
      
          if( this.bearer ) {
            config.headers["Authorization"] = `Bearer ${this.bearer}`;
          }

          if (multipart) {
            config.headers["Content-Type"] = `multipart/form-data`;
          }
          
          return config;
        }, function (error) {
          return Promise.reject(error);
        });
      
      
      if(single){
        const path = url.includes("?") ? url.substr(0, url.indexOf("?")) : url;
        if(typeof this.request_instances[path] != "undefined"){
            this.request_instances[path].cancel.cancel({message: "cancelled"})
        }
        this.request_instances[path] = {
          fn: this.axiosInstance.bind(window, { method, url, baseURL, data, }),
          cancel: source
        };
        return this.request_instances[path].fn;
      }else{
        return this.axiosInstance.bind(window, { method, url, baseURL, data, })
      }
      
      
    } else {
      return () => { new Promise((resolve) => resolve({ data })); };
    }
  } 

  cancelAllRequests() {
    Object.keys(this.request_instances).forEach(path => {
      this.request_instances[path].cancel.cancel({message: "aborted"})
    })
    
  }

  get(url, single = true) {
    const httpRequest = this.createHttpRequest('GET', url, null, single);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
      // await new Promise((resolve) => {
      //   setTimeout(resolve, 1200);
      // });

      return httpResponse;
    }).catch((error, response) => {
      ////console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');
      this.handleError(error, response)
      return {cancel: true};
    });
  }

  post(url, data, single = false, multipart = false) {
    const httpRequest = this.createHttpRequest('POST', url, data, single, multipart);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
    // await new Promise((resolve) => {
    //   setTimeout(resolve, 1200);
    // });

    return httpResponse;
    }).catch((error, response) => {
      ////console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');
      this.handleError(error, response)
      return {cancel: true};
    });
  }


  patch(url, data) {
    const httpRequest = this.createHttpRequest('PATCH', url, data);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
    // await new Promise((resolve) => {
    //   setTimeout(resolve, 1200);
    // });

    return httpResponse;
    }).catch((error, response) => {
      ////console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');
      this.handleError(error, response)
      return {cancel: true};
    });
  }

  put(url, data, multipart = false) {
    const httpRequest = this.createHttpRequest('PUT', url, data, false, multipart);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
    // await new Promise((resolve) => {
    //   setTimeout(resolve, 1200);
    // });

    return httpResponse;
    }).catch((error, response) => {
      ////console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');
      this.handleError(error, response)
      return {cancel: true};
    });
  }

  // patch alias
  update(url, data) {
    const httpRequest = this.createHttpRequest('PATCH', url, data);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
    // await new Promise((resolve) => {
    //   setTimeout(resolve, 1200);
    // });

    return httpResponse;
    }).catch((error, response) => {
      ////console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');
      this.handleError(error, response)
      return {cancel: true};
    });
  }


  delete(url, data) {
    const httpRequest = this.createHttpRequest('DELETE', url, data);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
    // await new Promise((resolve) => {
    //   setTimeout(resolve, 1200);
    // });

    return httpResponse;
    }).catch((error, response) => {
      this.handleError(error, response)
      return {cancel: true};
    });
  }

  response_valid(r){
    if(typeof r == 'undefined' || r.cancel){
      return false;
    }else{
      return true;
    }
  }

  handleError(error, response) {
    if (error.response && error.response.status === 401 && error.response.data.error === 'Invalid token') {
      document.cookie = 'token=; Max-Age=-99999999;';  
    }
  }

  get_user_status(url = `api/status`) {
    const httpRequest = this.createHttpRequest('GET', url, null, false);

    return httpRequest().then(async (httpResponse) => {
      // write here just before response is processed
      // await new Promise((resolve) => {
      //   setTimeout(resolve, 1200);
      // });
      return httpResponse.data.login == true;
    }).catch((error, response) => {
      //console.log(`%cHTTP GET Request error\r\n${baseURL + url}\r\n%c${error}`, 'background-color: #FFF; color: #FF0000; font-size: 18px; border: 2px solid #FF0000; padding: 8px;', 'background-color: #FFF; color: #333; font-size: 15px;');

      return error;
    });
  }

  cancelabePromise = promise => {
    let hasCanceled = false;
    const wrappedPromise = new Promise((resolve, reject) => {
      promise
        .then(val => (hasCanceled ? reject({ isCanceled: true }) : resolve(val)))
        .catch(
          error => (hasCanceled ? reject({ isCanceled: true }) : reject(error))
        );
    });

    return {
      promise: wrappedPromise,
      cancel() {
        hasCanceled = true;
      }
    }
  };
}

const request = new Request();
window.request = request;

export default request;