import axios from "axios";

// set your backend base URL (adjust port if needed)
// const api = axios.create({
//   baseURL: "http://127.0.0.1:8000/api", // Laravel backend API
//   headers: {
//     "Content-Type": "application/json",
//     Accept: "application/json",
//   },
// });
// export default api;  
// import axios from "axios";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;

