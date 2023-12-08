type TitleCardReq = {
  title: string;
};

type CreateCardReq = {
  columnId: number;
  boardId: number;
  reqData: TitleCardReq;
};

type DeleteCardReq = {
  columnId: number;
  boardId: number;
  cardId: number;
};

type ParamsApiCard = {
  columnId: number;
  boardId: number;
  cardId: number;
};

type AssignToMemberReq = {
  assignToMemberId: number;
};

type ChangeColumnReq = {
  destinationColumnId: number;
};
