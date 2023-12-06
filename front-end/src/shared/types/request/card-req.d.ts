type TitleCardReq = {
  title: string;
};

type CreateCardReq = {
  columnId: number;
  boardId: number;
  reqData: TitleCardReq;
};
