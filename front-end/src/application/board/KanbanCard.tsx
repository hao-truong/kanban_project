import CardService from '@/shared/services/CardService';
import { MAX_LENGTH_INPUT_TITLE_CARD, MIN_LENGTH_INPUT_STRING } from '@/shared/utils/constant';
import Helper from '@/shared/utils/helper';
import { yupResolver } from '@hookform/resolvers/yup';
import { OutlinedInput } from '@mui/material';
import { Check, MoreHorizontal, Pencil, X } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { SubmitHandler, useForm } from 'react-hook-form';
import { useQueryClient } from 'react-query';
import { toast } from 'react-toastify';
import * as yup from 'yup';
import MenuAssignUser from './MenuAssignUser';
import DialogDetailCard from './DialogDetailCard';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import BoardService from '@/shared/services/BoardService';

interface itemProps {
  card: Card;
  boardId: number;
  setIsDraggingCard: Function;
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
        MAX_LENGTH_INPUT_TITLE_CARD,
        `Title must be at least ${MAX_LENGTH_INPUT_TITLE_CARD} characters long`,
      ),
  })
  .required();

const KanbanCard = ({ card, boardId, setIsDraggingCard }: itemProps) => {
  const [isOver, setIsOver] = useState<boolean>(false);
  const { cardNeedDrop, setCardNeedDrop } = useGlobalState();
  const queryClient = useQueryClient();
  const [isShowMenu, setIsShowMenu] = useState<boolean>(false);
  const [isEditTitle, setIsEditTitle] = useState<boolean>(false);
  const titleRef = useRef<HTMLFormElement | null>(null);
  const menuRef = useRef<HTMLUListElement | null>(null);
  const [isOpenMenuAssignUser, setIsOpenMenuAssignUser] = useState<boolean>(false);
  const [isOpenDetailCard, setIsOpenDetailCard] = useState<boolean>(false);
  const {
    register,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm<UpdateColumnReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<UpdateColumnReq> = (reqData) => {
    if (reqData.title === card.title) {
      return;
    }

    CardService.updateTitleCard(
      {
        cardId: card.id,
        columnId: card.column_id,
        boardId,
      },
      reqData,
    )
      .then(() => {
        queryClient.invalidateQueries(`getCards${card.column_id}`);
        toast.success('Update card successfully!');
        setIsEditTitle(false);
      })
      .catch((responseError: ResponseError) => {
        toast.error(responseError.error);
      });
  };

  useEffect(() => {
    if (!isEditTitle) {
      setValue('title', card.title);
    }
  }, [isEditTitle, card]);

  useEffect(() => {
    Helper.handleOutSideClick(menuRef, setIsShowMenu);
  }, [isShowMenu]);

  const handleDelete = () => {
    CardService.deleteCard({
      columnId: card.column_id,
      boardId,
      cardId: card.id,
    })
      .then((response) => {
        const { data } = response;
        queryClient.invalidateQueries(`getCards${card.column_id}`);
        toast.success(data);
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));
  };

  useEffect(() => {
    if (!cardNeedDrop) {
      setIsOver(false);
    }
  }, [cardNeedDrop]);

  const handleDragStart = (e: React.DragEvent<HTMLDivElement>, card: Card) => {
    e.stopPropagation();
    setCardNeedDrop(card);
  };

  const handleDragOver = (e: React.DragEvent<HTMLDivElement>, card: Card) => {
    e.preventDefault();

    if (!cardNeedDrop || (cardNeedDrop && card.id === cardNeedDrop.id)) {
      setIsOver(false);
      return;
    }

    setIsOver(true);
  };

  const handleDragEnd = () => {
    setCardNeedDrop(null);
    setIsOver(false);
    setIsDraggingCard(false);
  };

  const handleDrop = (e: React.DragEvent<HTMLDivElement>, card: Card) => {
    e.preventDefault();
    setIsOver(false);

    if (!cardNeedDrop) {
      return;
    }

    if (cardNeedDrop.id === card.id) {
      return;
    }

    BoardService.moveCards(boardId, {
      originalCardId: cardNeedDrop.id,
      originalColumnId: cardNeedDrop.column_id,
      targetCardId: card.id,
      targetColumnId: card.column_id,
    })
      .then(() => {
        if (card.column_id === cardNeedDrop.column_id) {
          queryClient.invalidateQueries(`getCards${card.column_id}`);
          return;
        }

        queryClient.invalidateQueries(`getCards${card.column_id}`);
        queryClient.invalidateQueries(`getCards${cardNeedDrop.column_id}`);
      })
      .catch((responseError: ResponseError) => toast.error(responseError.error));
  };

  return (
    <div>
      <div
        className="w-full relative grid grid-cols-12 bg-white rounded-md p-4 gap-2 cursor-pointer hover:bg-slate-400"
        draggable
        onDragStart={(e) => handleDragStart(e, card)}
        onDragOver={(e) => handleDragOver(e, card)}
        onDragLeave={() => setIsOver(false)}
        onDragEnd={handleDragEnd}
        onDrop={(e) => handleDrop(e, card)}
        onClick={() => setIsOpenDetailCard(true)}
      >
        {isOver && (
          <div className="h-[5px] w-full absolute bg-red-700 top-0 -translate-y-2.5 rounded-lg"></div>
        )}
        <div className="col-span-10">
          <div className="">
            {!isEditTitle && (
              <h2 className="break-words text-left hover:decoration-solid hover:underline relative">
                <span>{card.title}</span>
                <Pencil
                  className="absolute bottom-0 right-0 hover:bg-slate-200 p-1"
                  size={25}
                  onClick={(e) => {
                    e.stopPropagation();
                    setIsEditTitle(true);
                  }}
                />
              </h2>
            )}
            {isEditTitle && (
              <form ref={titleRef} className="relative" onSubmit={handleSubmit(onSubmit)}>
                <OutlinedInput
                  autoFocus
                  className="w-full bg-white"
                  onFocus={() => Helper.handleOutSideClick(titleRef, setIsEditTitle)}
                  onClick={(e) => e.stopPropagation()}
                  id="outlined-adornment-weight"
                  aria-describedby="outlined-weight-helper-text"
                  {...register('title')}
                  error={errors.title ? true : false}
                  inputProps={{ maxLength: MAX_LENGTH_INPUT_TITLE_CARD }}
                />
                <div className="flex flex-row justify-end gap-2 mt-2 absolute right-0">
                  <button type="submit">
                    <Check
                      onClick={(e) => e.stopPropagation()}
                      size={40}
                      className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                    />
                  </button>
                  <X
                    size={40}
                    className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer"
                    onClick={(e) => {
                      e.stopPropagation();
                      setIsEditTitle(false);
                    }}
                  />
                </div>
              </form>
            )}
          </div>
        </div>
        <div className="col-span-2 flex flex-col justify-between items-end gap-4">
          <div className="relative w-fit bg-slate-200 ">
            <MoreHorizontal
              className="cursor-pointer hover:bg-slate-100 p-2"
              size={40}
              onClick={(e) => {
                e.stopPropagation();
                setIsShowMenu(!isShowMenu);
              }}
            />
            {isShowMenu && (
              <ul
                ref={menuRef}
                className="absolute bg-white top-full right-0 py-1 w-max shadow-lg border border-black z-10"
              >
                <div>
                  <li
                    className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer"
                    onClick={(e) => {
                      e.stopPropagation();
                      handleDelete();
                    }}
                  >
                    Delete
                  </li>
                </div>
              </ul>
            )}
          </div>
          <div className="w-full relative">
            <h2
              className="p-2 bg-yellow-400"
              title={card.assigned_user ? card.assigned_user.username.slice(0, 2) : 'unassigned'}
              onClick={(e) => {
                e.stopPropagation();
                setIsOpenMenuAssignUser(!isOpenMenuAssignUser);
              }}
            >
              {card.assigned_user ? card.assigned_user.username.slice(0, 2) : '#'}
            </h2>
            {isOpenMenuAssignUser && (
              <MenuAssignUser
                card={card}
                boardId={boardId}
                isOpen={isOpenMenuAssignUser}
                setIsOpen={setIsOpenMenuAssignUser}
              />
            )}
          </div>
        </div>
      </div>
      {card && (
        <DialogDetailCard
          card={card}
          isOpen={isOpenDetailCard}
          setIsOpen={setIsOpenDetailCard}
          boardId={boardId}
        />
      )}
    </div>
  );
};

export default KanbanCard;
