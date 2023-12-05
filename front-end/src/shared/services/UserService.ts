import axiosClient from '../libs/axios';

const UserService = {
  getProfile: () => axiosClient.get<User>(`/users/me`),
};

export default UserService;
