import axiosClient from '../libs/axios';

const BoardService = {
  getMyBoards: () => axiosClient.get<Board[]>(`/boards/me`),
  createBoard: (data: BoardReq) => axiosClient.post<Board>(`/boards`, data),
  updateBoard: (boardId: number, data: BoardReq) =>
    axiosClient.patch<Board>(`/boards/${boardId}`, data),
  deleteBoard: (boardId: number) => axiosClient.delete<string>(`/boards/${boardId}`),
  getBoard: (boardId: number) => axiosClient.get<Board>(`/boards/${boardId}`),
  addMemberToBoard: (boardId: number, data: MemberToAddReq) =>
    axiosClient.post<string>(`/boards/${boardId}/members`, data),
  leaveBoard: (boardId: number) => axiosClient.delete<string>(`/boards/${boardId}/members/leave`),
  getMembers: (boardId: number) => axiosClient.get<User[]>(`/boards/${boardId}/members`),
  moveCards: (boardId: number, data: MoveCardsReq) =>
    axiosClient.patch<string>(`/boards/${boardId}/move-cards`, data),
};

export default BoardService;
