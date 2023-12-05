import axiosClient from '../libs/axios';

const ColumnService = {
  createColumn: (boardId: number, data: ColumnReq) =>
    axiosClient.post<Column>(`/boards/${boardId}/columns`, data),
  getColumnsOfBoard: (boardId: number) => axiosClient.get<Column[]>(`/boards/${boardId}/columns`),
};

export default ColumnService;
