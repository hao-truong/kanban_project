import axiosClient from "../libs/axios";

const BoardService =  {
    getMyBoards: () => axiosClient.get<Board[]>(`/boards/me`),
    createBoard: (data: BoardReq) => axiosClient.post<Board>(`/boards`, data),
    updateBoard: (boardId: number, data: BoardReq) => axiosClient.patch<Board>(`/boards/${boardId}`, data),
    deleteBoard: (boardId: number) => axiosClient.delete<string>(`/boards/${boardId}`),
    getBoard: (boardId: number) => axiosClient.get<Board>(`/boards/${boardId}`),
};

export default BoardService;