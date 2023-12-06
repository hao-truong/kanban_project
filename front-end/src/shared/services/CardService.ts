import axiosClient from '../libs/axios';

const CardService = {
  getCards: (boardId: number, columnId: number) =>
    axiosClient.get<Card[]>(`/boards/${boardId}/columns/${columnId}/cards`),
  createCard: (data: CreateCardReq) =>
    axiosClient.post<Card>(`/boards/${data.boardId}/columns/${data.columnId}/cards`, data.reqData),
};

export default CardService;
