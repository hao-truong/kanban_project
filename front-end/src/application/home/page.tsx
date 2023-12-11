import { useState } from 'react';
import KanbanBoard from './KanbanBoard';
import { InputBase } from '@mui/material';
import { Plus, Search } from 'lucide-react';
import DialogCreateBoard from './DialogCreateBoard';
import { useQuery, useQueryClient } from 'react-query';
import useCheckLogin from '@/shared/hooks/useCheckLogin';
import { getMyBoards } from '@/shared/services/QueryService';

const HomePage = () => {
  useQueryClient();
  const [isOpenDialogCreateBoard, setIsOpenDialogCreateBoard] = useState<boolean>(false);
  const isLogin = useCheckLogin();
  const { data: boards } = useQuery<Board[]>('getMyBoards', getMyBoards, {
    enabled: isLogin,
  });

  return (
    <div className="">
      <h2 className="w-full text-center font-bold text-5xl my-10">YOUR BOARDS</h2>
      <div className="flex flex-row justify-between items-center">
        <div className="flex flex-row items-center gap-4 w-fit my-5 bg-slate-100 px-4">
          <InputBase
            className="py-3"
            sx={{ ml: 1, flex: 1 }}
            placeholder="Search board"
            inputProps={{ 'aria-label': 'search google maps' }}
          />
          <Search className="cursor-pointer" size={25} />
        </div>
        <div
          className="h-fit flex flex-row items-center gap-4 px-4 py-2 cursor-pointer hover:bg-slate-400"
          onClick={() => setIsOpenDialogCreateBoard(!isOpenDialogCreateBoard)}
        >
          <Plus />
          <span>Create board</span>
          <DialogCreateBoard
            isOpen={isOpenDialogCreateBoard}
            setIsOpen={setIsOpenDialogCreateBoard}
          />
        </div>
      </div>
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        {boards &&
          boards?.length !== 0 &&
          boards.map((board) => <KanbanBoard board={board} key={board.id} />)}
      </div>
      {boards?.length === 0 && <div className="text-center text-xl">You don't have any board.</div>}
    </div>
  );
};

export default HomePage;
