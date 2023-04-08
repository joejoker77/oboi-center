import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import {Dropdown, Tab, Modal, Button, Collapse, Tooltip} from "bootstrap";
import Choices from "choices.js";

window.Modal = Modal;
window.Collapse = Collapse;
window.Tooltip = Tooltip;
window.Tab = Tab;


