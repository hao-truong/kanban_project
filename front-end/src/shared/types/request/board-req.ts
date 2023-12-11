type BoardReq = {
  title: string;
};

type MemberToAddReq = {
  member: string;
};

type MoveCardsReq = {
  originalCardId: number;
  originalColumnId: number;
  targetCardId: number;
  targetColumnId: number;
};
