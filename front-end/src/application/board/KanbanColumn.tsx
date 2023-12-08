import {
  MAX_LENGTH_INPUT_STRING,
  MAX_TITLE_LENGTH,
  MIN_LENGTH_INPUT_STRING,
} from '@/shared/utils/constant';
import { yupResolver } from '@hookform/resolvers/yup';
import { Check, MoreHorizontal, Plus, X } from 'lucide-react';
import { SubmitHandler, useForm } from 'react-hook-form';
import { toast } from 'react-toastify';
import * as yup from 'yup';
import { useQuery, useQueryClient } from 'react-query';
import Helper from '@/shared/utils/helper';
import { useEffect, useRef, useState } from 'react';
import { OutlinedInput } from '@mui/material';
import ColumnService from '@/shared/services/ColumnService';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import KanbanCard from './KanbanCard';
import DialogCreateCard from './DialogCreateCard';
import { getCards } from '@/shared/services/QueryService';

interface itemProps {
  column: Column;
}

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

const KanbanColumn = ({ column }: itemProps) => {
  const queryClient = useQueryClient();
  const titleRef = useRef<HTMLFormElement | null>(null);
  const [isEnableDraggable, setIsEnableDraggable] = useState<boolean>(true);
  const [isClickTilte, setIsClickTitle] = useState<boolean>(false);
  const [isShowMenu, setIsShowMenu] = useState<boolean>(false);
  const [isHoverTitle, setIsHoverTitle] = useState<boolean>(false);
  const [isOver, setIsOver] = useState<boolean>(false);
  const [isOpenDialogCreateCard, setIsOpenDialogCreateCard] = useState<boolean>(false);
  const { data: cards } = useQuery<Card[]>(
    `getCards${column.id}`,
    () => getCards(column.board_id, column.id),
    {
      enabled: !!column,
    },
  );
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm<UpdateColumnReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<UpdateColumnReq> = async (reqData) => {
    if (reqData.title === column.title) {
      setIsClickTitle(false);
      return;
    }

    const data = await ColumnService.updateColumn(column.id, column.board_id, reqData)
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (data) {
      toast.success('Update column successfully!');
      setIsClickTitle(false);
      queryClient.invalidateQueries('getColumnsOfBoard');
    }
  };

  useEffect(() => {
    if (!isClickTilte) {
      setValue('title', column.title);
    }

    if (!isHoverTitle) {
      setIsShowMenu(false);
    }
  }, [isClickTilte, isHoverTitle, column]);

  const handleDeleteColumn = async () => {
    const data = await ColumnService.deleteColumn(column.id, column.board_id)
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (data) {
      queryClient.invalidateQueries('getColumnsOfBoard');
      toast.success(data);
    }
  };

  const { columnNeedDrop, setColumnNeedDrop } = useGlobalState();

  const handleDragStart = (column: Column) => {
    setColumnNeedDrop(column);
  };

  const handleDragOver = (e: any, column: Column) => {
    e.preventDefault();

    if (!columnNeedDrop || (columnNeedDrop && column.id === columnNeedDrop.id)) {
      setIsOver(false);
      return;
    }

    setIsOver(true);
  };

  const handleDragEnd = async () => {
    setColumnNeedDrop(null);
    setIsOver(false);
  };

  const handleDrop = async (e: any, targetColumn: Column) => {
    e.preventDefault();

    if (!columnNeedDrop) {
      return;
    }

    if (columnNeedDrop.id === targetColumn.id) {
      return;
    }

    const data = await ColumnService.swapPositionOfCoupleColumn(column.board_id, {
      originalColumnId: columnNeedDrop.id,
      targetColumnId: targetColumn.id,
    })
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (data) {
      queryClient.invalidateQueries('getColumnsOfBoard');
    }
    setIsOver(false);
  };

  return (
    <div className={`flex flex-row relative ${isEnableDraggable ? 'cursor-grabbing' : ''}`}>
      {isOver && (
        <div className="w-[5px] h-full absolute bg-red-700 left-0 top-0 -translate-x-2.5 rounded-lg"></div>
      )}
      <div
        className="bg-slate-200 p-4 text-center flex flex-col gap-5 rounded-xl min-w-[350px] w-[350px] min-h-[300px]"
        draggable={isEnableDraggable}
        onDragStart={() => handleDragStart(column)}
        onDragOver={(e) => handleDragOver(e, column)}
        onDragLeave={() => setIsOver(false)}
        onDragEnd={handleDragEnd}
        onDrop={(e) => handleDrop(e, column)}
      >
        <div onMouseOver={() => setIsHoverTitle(true)} onMouseLeave={() => setIsHoverTitle(false)}>
          {isClickTilte && (
            <form ref={titleRef} className="relative" onSubmit={handleSubmit(onSubmit)}>
              <OutlinedInput
                autoFocus
                className="w-full bg-white"
                onFocus={() => Helper.handleOutSideClick(titleRef, setIsClickTitle)}
                id="outlined-adornment-weight"
                aria-describedby="outlined-weight-helper-text"
                {...register('title')}
                error={errors.title ? true : false}
                inputProps={{ maxLength: MAX_TITLE_LENGTH }}
              />
              <div className="flex flex-row justify-end gap-2 mt-2 absolute right-0 z-10">
                <button type="submit">
                  <Check
                    size={40}
                    className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                  />
                </button>
                <X
                  size={40}
                  className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                  onClick={() => setIsClickTitle(false)}
                />
              </div>
            </form>
          )}
          {!isClickTilte && (
            <div className="flex flex-row justify-between items-center gap-4">
              <h2
                className="w-full uppercase text-xl font-bold py-2 hover:bg-white cursor-pointer text-left"
                onClick={() => {
                  setIsClickTitle(true);
                }}
              >
                {column.title}
              </h2>
              <div className="relative">
                <MoreHorizontal
                  className="cursor-pointer hover:bg-slate-100 p-2 relative"
                  size={40}
                  onClick={() => setIsShowMenu(!isShowMenu)}
                  onMouseOver={() => setIsEnableDraggable(false)}
                  onMouseLeave={() => setIsEnableDraggable(true)}
                />
                {isShowMenu && (
                  <ul className="absolute bg-white top-full right-0 py-1 w-max border border-black z-10">
                    <div>
                      <li
                        className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer"
                        onClick={handleDeleteColumn}
                      >
                        Delete
                      </li>
                    </div>
                  </ul>
                )}
              </div>
            </div>
          )}
        </div>
        <DialogCreateCard
          isOpen={isOpenDialogCreateCard}
          setIsOpen={setIsOpenDialogCreateCard}
          columnId={column.id}
          boardId={column.board_id}
        />
        <div className="h-full max-h-[500px] overflow-y-scroll">
          <div className="flex flex-col gap-4">
            {cards &&
              cards.length !== 0 &&
              cards.map((card) => (
                <KanbanCard card={card} key={card.id} boardId={column.board_id} />
              ))}
          </div>
          <button
            className="w-full mt-4 flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400"
            onClick={() => setIsOpenDialogCreateCard(true)}
            onMouseOver={() => setIsEnableDraggable(false)}
            onMouseLeave={() => setIsEnableDraggable(true)}
          >
            <Plus />
            <span>Create card</span>
          </button>
        </div>
      </div>
    </div>
  );
};

export default KanbanColumn;
