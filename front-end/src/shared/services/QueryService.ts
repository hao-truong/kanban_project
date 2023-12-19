import BoardService from './BoardService';
import CardService from './CardService';
import ColumnService from './ColumnService';

export const getColumnsOfBoard = async (boardId: number): Promise<Column[]> => {
  const data = await ColumnService.getColumnsOfBoard(boardId).then((response) => response.data);
  return data;
};

export const getBoard = async (boardId: number): Promise<Board> => {
  const data = await BoardService.getBoard(boardId).then((response) => response.data);
  return data;
};

export const getMembers = async (boardId: number): Promise<User[]> => {
  const data = await BoardService.getMembers(boardId).then((response) => response.data);

  return data;
};

export const getMyBoards = async (): Promise<Board[]> => {
  const data = await BoardService.getMyBoards().then((response) => response.data);
  return data;
};

export const getCards = async (boardId: number, columnId: number): Promise<Card[]> => {
  const data = await CardService.getCards(boardId, columnId).then((response) => response.data);
  return data;
};
