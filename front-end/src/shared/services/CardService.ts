import axiosClient from '../libs/axios';

const CardService = {
  getDetailCard: (params: ParamsApiCard) =>
    axiosClient.get<Card>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}`,
    ),
  getCards: (boardId: number, columnId: number) =>
    axiosClient.get<Card[]>(`/boards/${boardId}/columns/${columnId}/cards`),
  createCard: (data: CreateCardReq) =>
    axiosClient.post<Card>(`/boards/${data.boardId}/columns/${data.columnId}/cards`, data.reqData),
  updateTitleCard: (params: ParamsApiCard, data: TitleCardReq) =>
    axiosClient.patch<Card>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}`,
      data,
    ),
  deleteCard: (params: ParamsApiCard) =>
    axiosClient.delete<string>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}`,
    ),
  assignMe: (params: ParamsApiCard) =>
    axiosClient.patch<string>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}/assign-to-me`,
    ),
  assignToMember: (params: ParamsApiCard, data: AssignToMemberReq) =>
    axiosClient.patch<string>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}/assign-to-member`,
      data,
    ),
  changeColumnForCard: (params: ParamsApiCard, data: ChangeColumnReq) =>
    axiosClient.patch<string>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}/change-column`,
      data,
    ),
  updateDescriptionOfCard: (params: ParamsApiCard, data: DescriptionCardReq) =>
    axiosClient.patch<Card>(
      `/boards/${params.boardId}/columns/${params.columnId}/cards/${params.cardId}/update-description`,
      data,
    ),
};

export default CardService;
