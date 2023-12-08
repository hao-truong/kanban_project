type TitleCardReq = {
  title: string;
};

type CreateCardReq = {
  columnId: number;
  boardId: number;
  reqData: TitleCardReq;
};

type UpdateTitleCardReq = {
  columnId: number;
  boardId: number;
  cardId: number;
  reqData: TitleCardReq;
};

type DeleteCardReq = {
  columnId: number;
  boardId: number;
  cardId: number;
};
