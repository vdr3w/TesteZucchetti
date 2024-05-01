import axios from 'axios';
import router from './router';

const api = axios.create({
  baseURL: 'http://localhost:8000'
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('@tokenzucchetti');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => Promise.reject(error));

api.interceptors.response.use(response => response, error => {
  if (error.response && error.response.status === 401) {
    router.push('/unauthorized');
  }
  return Promise.reject(error);
});

export default api;
