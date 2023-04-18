import { createBrowserHistory, } from 'history';
import config from '../config';
import request from './request';

const history = createBrowserHistory();

history.customChangeEvent = () => {}

history.setCustomChangeEvent = (fn) => {
  history.customChangeEvent = fn
}

history.listen((e) => {
  window.scroll(0, 0);
  history.last = e.pathname;
  history.customChangeEvent()
  // request.cancelAllRequests()
});

export default history;