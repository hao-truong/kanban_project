import axiosClient from '../libs/axios';

const ColumnService = {
  createColumn: (boardId: number, data: ColumnReq) =>
    axiosClient.post<Column>(`/boards/${boardId}/columns`, data),
  getColumnsOfBoard: (boardId: number) => axiosClient.get<Column[]>(`/boards/${boardId}/columns`),
  updateColumn: (columnId: number, boardId: number, data: ColumnReq) =>
    axiosClient.patch<Column>(`/boards/${boardId}/columns/${columnId}`, data),
  deleteColumn: (columnId: number, boardId: number) =>
    axiosClient.delete<string>(`/boards/${boardId}/columns/${columnId}`),
  swapPositionOfCoupleColumn: (boardId: number, data: DragDropReq) =>
    axiosClient.patch<string>(`/boards/${boardId}/columns/position`, data),
};

export default ColumnService;
