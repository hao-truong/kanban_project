import axiosClient from "../libs/axios";

const AuthService = {
  sigin: (data: SigninReq) => axiosClient.post<AuthToken>(`/auth/login`, data),
  register: (data: RegisterReq) =>
    axiosClient.post<string>(`/auth/register`, data),
  logout: () => axiosClient.post<string>(`/auth/logout`),
};

export default AuthService;