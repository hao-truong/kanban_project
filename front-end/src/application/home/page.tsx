import { useState } from 'react';
import KanbanBoard from './KanbanBoard';
import { InputBase } from '@mui/material';
import { Plus, Search } from 'lucide-react';
import DialogCreateBoard from './DialogCreateBoard';
import { useQuery, useQueryClient } from 'react-query';
import useCheckLogin from '@/shared/hooks/useCheckLogin';
import { getMyBoards } from '@/shared/services/QueryService';
import * as yup from 'yup';
import { MAX_LENGTH_INPUT_STRING, MIN_LENGTH_INPUT_STRING } from '@/shared/utils/constant';
import { SubmitHandler, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import BoardService from '@/shared/services/BoardService';
import { toast } from 'react-toastify';

const schemaValidation = yup
  .object({
    title: yup
      .string()
      .trim()
      .required('Title is required!')
      .min(
        MIN_LENGTH_INPUT_STRING,
        `Title must be at least ${MIN_LENGTH_INPUT_STRING} characters long`,
      )
      .max(
        MAX_LENGTH_INPUT_STRING,
        `Title must be at least ${MAX_LENGTH_INPUT_STRING} characters long`,
      ),
  })
  .required();

const HomePage = () => {
  useQueryClient();
  const [isOpenDialogCreateBoard, setIsOpenDialogCreateBoard] = useState<boolean>(false);
  const isLogin = useCheckLogin();
  const [boards, setBoards] = useState<Board[]>([]);
  useQuery<Board[]>('getMyBoards', getMyBoards, {
    enabled: isLogin,
    onSuccess: (data) => {
      setBoards(data);
    },
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<SearchBoardReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<UpdateColumnReq> = async (reqData) => {
    const data = await BoardService.searchBoard(reqData)
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (Array.isArray(data)) {
      setBoards(data);
    }
  };

  return (
    <div className="">
      <h2 className="w-full text-center font-bold text-5xl my-10">YOUR BOARDS</h2>
      <div className="flex flex-row justify-between items-center">
        <form
          className="flex flex-row items-center gap-4 w-fit my-5 bg-slate-100 px-4"
          onSubmit={handleSubmit(onSubmit)}
        >
          <InputBase
            className="py-3"
            sx={{ ml: 1, flex: 1 }}
            placeholder="Search board's title..."
            {...register('title')}
            error={errors.title ? true : false}
            inputProps={{ maxLength: MAX_LENGTH_INPUT_STRING }}
          />
          <button type="submit">
            <Search className="cursor-pointer" size={25} />
          </button>
        </form>
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
