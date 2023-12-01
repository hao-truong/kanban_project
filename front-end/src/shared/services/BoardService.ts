import axiosClient from "../libs/axios";

const BoardService =  {
    getMyBoards: () => axiosClient.get<Board[]>(`/boards/me`),
    createBoard: (data: BoardReq) => axiosClient.post<Board>(`/boards`, data),
};

export default BoardService;