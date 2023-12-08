import axiosClient from '../libs/axios';

const CardService = {
  getCards: (boardId: number, columnId: number) =>
    axiosClient.get<Card[]>(`/boards/${boardId}/columns/${columnId}/cards`),
  createCard: (data: CreateCardReq) =>
    axiosClient.post<Card>(`/boards/${data.boardId}/columns/${data.columnId}/cards`, data.reqData),
  updateTitleCard: (data: UpdateTitleCardReq) =>
    axiosClient.patch<Card>(
      `/boards/${data.boardId}/columns/${data.columnId}/cards/${data.cardId}`,
      data.reqData,
    ),
  deleteCard: (params: DeleteCardReq) =>
    axiosClient.delete<string>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}`,
    ),
};

export default CardService;
