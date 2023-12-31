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
import SpinnerLoading from '@/shared/components/SpinnerLoading';

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
  const [isLoading, setIsLoading] = useState<boolean>(true);
  useQuery<Board[]>('getMyBoards', getMyBoards, {
    enabled: isLogin,
    onSuccess: (data) => {
      setBoards(data);
      setIsLoading(false);
    },
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<SearchBoardReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<UpdateColumnReq> = (reqData) => {
    setIsLoading(true);
    BoardService.searchBoard(reqData)
      .then((response) => {
        const { data } = response;
        if (Array.isArray(data)) {
          setBoards(data);
          setIsLoading(false);
        }
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));
  };

  return (
    <div className="">
      <h2 className="w-full text-center font-bold text-5xl my-10">YOUR BOARDS</h2>
      <div className="flex flex-row justify-between items-center my-10">
        <form
          className="flex flex-row w-fit my-5 bg-slate-100 relative px-2"
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
          <button type="submit" className="px-4">
            <Search className="cursor-pointer" size={25} />
          </button>
          {errors.title && (
            <span className="absolute -bottom-5 text-red-600 text-sm ml-2 w-screen">
              {errors.title.message}
            </span>
          )}
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
      {isLoading && <SpinnerLoading />}
      {!isLoading && (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
          {boards &&
            boards?.length !== 0 &&
            boards.map((board) => <KanbanBoard board={board} key={board.id} />)}
        </div>
      )}
      {!isLoading && boards?.length === 0 && (
        <div className="text-center text-xl">You don't have any board.</div>
      )}
    </div>
  );
};

export default HomePage;
