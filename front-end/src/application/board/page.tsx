import { Plus } from 'lucide-react';
import KanbanColumn from './KanbanColumn';
import { useEffect, useState } from 'react';
import useCheckLogin from '@/shared/hooks/useCheckLogin';
import { useNavigate, useParams } from 'react-router-dom';
import BoardService from '@/shared/services/BoardService';
import { useQuery, useQueryClient } from 'react-query';
import ColumnService from '@/shared/services/ColumnService';
import DialogCreateColumn from './DialogCreateColumn';
import FlipMove from 'react-flip-move';

const getColumnsOfBoard = async (boardId: number): Promise<Column[]> => {
  const data = await ColumnService.getColumnsOfBoard(boardId).then((response) => response.data);
  return data;
};

const getBoard = async (boardId: number): Promise<Board> => {
  const data = await BoardService.getBoard(boardId).then((response) => response.data);
  return data;
};

const BoardPage = () => {
  useQueryClient();
  const isLogin = useCheckLogin();
  const navigate = useNavigate();
  const params = useParams<{ boardId: string }>();
  const [isShowDialogCreateColumn, setIsShowDialogCreateColumn] = useState<boolean>(false);
  const { data: columns } = useQuery<Column[]>(
    'getColumnsOfBoard',
    () => getColumnsOfBoard(Number(params.boardId)),
    {
      enabled: !!params.boardId,
    },
  );
  const { data: board } = useQuery<Board | null>(
    'getBoard',
    () => getBoard(Number(params.boardId)),
    {
      enabled: !!params.boardId,
    },
  );

  useEffect(() => {
    if (!isLogin) {
      navigate('/auth/sign-in');
    }
  }, [isLogin]);

  const renderColumns = () => {
    return (
      <FlipMove
        className="flip-wrapper"
        duration={400}
        delay={10}
        easing={'cubic-bezier(.12,.36,.14,1.2)'}
        staggerDurationBy={30}
        staggerDelayBy={150}
        appearAnimation="accordionHorizontal"
        enterAnimation="fade"
        leaveAnimation="fade"
      >
        <div className="flex flex-row gap-4 overflow-auto px-3">
          {columns &&
            columns.length !== 0 &&
            columns.map((column) => <KanbanColumn column={column} key={column.id} />)}
        </div>
      </FlipMove>
    );
  };

  return (
    <div>
      <div className="flex flex-row justify-between my-10">
        <h2 className="uppercase">{board?.title}</h2>
        <button
          className="flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400"
          onClick={() => setIsShowDialogCreateColumn(!isShowDialogCreateColumn)}
        >
          <Plus />
          <span>Create column</span>
        </button>
        {board && (
          <DialogCreateColumn
            isOpen={isShowDialogCreateColumn}
            setIsOpen={setIsShowDialogCreateColumn}
            boardId={board.id}
          />
        )}
      </div>
      {renderColumns()}
      {columns && columns.length === 0 && (
        <div className="text-center text-xl">Don't have any column in this board.</div>
      )}
    </div>
  );
};

export default BoardPage;
