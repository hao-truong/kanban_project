import axios from "axios";
import JwtStorage from "../storages/JwtStorage";

const axiosClient = axios.create({
    baseURL: import.meta.env.VITE_BACKEND_URL,
});

axiosClient.interceptors.request.use(
    (config) => {
        config.headers["Authorization"] = `Bearer ${JwtStorage.getToken()?.accessToken
            }`;
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

axiosClient.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const responseError: ResponseError = error.response.data;

        if(responseError.statusCode === 403) {
            JwtStorage.deleteToken();
            window.location.href = "/auth/sign-in";
            return;
        }

        return Promise.reject<ResponseError>(responseError);
    }
);

export default axiosClient;