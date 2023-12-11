import CardService from '@/shared/services/CardService';
import { X } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useQuery, useQueryClient } from 'react-query';
import ReactQuill from 'react-quill';
import { toast } from 'react-toastify';
import MenuAssignUser from './MenuAssignUser';
import { getColumnsOfBoard } from '@/shared/services/QueryService';
import * as yup from 'yup';
import { MAX_LENGTH_INPUT_TITLE_CARD, MIN_LENGTH_INPUT_STRING } from '@/shared/utils/constant';
import { SubmitHandler, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';

interface itemProps {
  card: Card;
  isOpen: boolean;
  setIsOpen: Function;
  boardId: number;
}

const schemaValidation = yup
  .object({
    description: yup
      .string()
      .trim()
      .required('Title is required')
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

const DialogDetailCard = ({ card, isOpen, setIsOpen, boardId }: itemProps) => {
  const queryClient = useQueryClient();
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);
  const [isShowRichTextEditor, setIsShowRichTextEditor] = useState<boolean>(false);
  const [isOpenMenuAssignUser, setIsOpenMenuAssignUser] = useState<boolean>(false);
  const [columnId, setColumnId] = useState<number>(card.column_id);
  const { data: columns } = useQuery<Column[]>(
    `getColumnsOfBoard${boardId}`,
    () => getColumnsOfBoard(boardId),
    {
      enabled: !!boardId,
    },
  );
  const column = columns?.find((column) => column.id === card.column_id);

  const {
    getValues,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<DescriptionCardReq>({
    resolver: yupResolver(schemaValidation),
  });
  const onSubmit: SubmitHandler<DescriptionCardReq> = async (dataReq) => {
    await CardService.updateDescriptionOfCard(
      {
        cardId: card.id,
        columnId: card.column_id,
        boardId,
      },
      dataReq,
    )
      .then(() => {
        queryClient.invalidateQueries(`getCards${card.column_id}`);
        setIsShowRichTextEditor(false);
        toast.success('Update card sucessfully!');
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));
  };

  useEffect(() => {
    if (isOpen && dialogRef) {
      dialogRef.current?.showModal();
    }

    const handleOutsideClick = (event: any) => {
      if (
        dialogRef.current &&
        bodyDialogRef.current &&
        !bodyDialogRef.current.contains(event.target)
      ) {
        dialogRef.current?.close();
        setIsOpen(false);
      }
    };

    document.addEventListener('mousedown', handleOutsideClick);

    return () => {
      document.removeEventListener('mousedown', handleOutsideClick);
    };
  }, [isOpen, dialogRef, bodyDialogRef]);

  useEffect(() => {
    setValue('description', card.description);
  }, [card]);

  const handleAssignToMe = async () => {
    await CardService.assignMe({
      cardId: card.id,
      columnId: card.column_id,
      boardId,
    })
      .then((response) => {
        const { data } = response;
        queryClient.invalidateQueries(`getCards${card.column_id}`);
        setIsOpen(false);
        toast.success(data);
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));
  };

  const handleMoveCardToAnotherColumn = async (selectedColumnId: number) => {
    setColumnId(selectedColumnId);
    await CardService.changeColumnForCard(
      {
        columnId: card.column_id,
        cardId: card.id,
        boardId,
      },
      { destinationColumnId: selectedColumnId },
    )
      .then(() => {
        queryClient.invalidateQueries(`getCards${card.column_id}`);
        queryClient.invalidateQueries(`getCards${selectedColumnId}`);
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));
  };

  return (
    <dialog
      ref={dialogRef}
      className="rounded-lg p-10 min-w-[90%] overflow-y-visible min-h-[400px] z-20 cursor-default"
    >
      <div className="flex flex-row justify-between my-2 items-center">
        {column && (
          <h2 className="flex flex-row gap-2 items-center ju">
            <span>{column.title.toUpperCase()}</span>
            <span>/</span>
            <span className="line line-clamp-1">{card.title}</span>
          </h2>
        )}
        <div className="p-1 hover:bg-slate-400 cursor-pointer" onClick={() => setIsOpen(false)}>
          <X size={40} />
        </div>
      </div>
      <div ref={bodyDialogRef} className="grid grid-cols-12 gap-4">
        <div className="col-span-12 lg:col-span-8 flex flex-col items-start text-left gap-4">
          <div className="text-3xl w-[90%] break-words">{card.title}</div>
          <div className="w-full flex flex-col gap-2">
            <strong>Description</strong>
            {!isShowRichTextEditor && (
              <div
                className="text-slate-500 py-2 hover:bg-slate-500 hover:text-white cursor-text"
                onClick={() => setIsShowRichTextEditor(true)}
                dangerouslySetInnerHTML={{
                  __html: card.description ? card.description : 'Add a description...',
                }}
              ></div>
            )}
            {isShowRichTextEditor && (
              <form className="flex flex-col gap-4" onSubmit={handleSubmit(onSubmit)}>
                {errors.description && <h2>{errors.description.message}</h2>}
                <div>
                  <ReactQuill
                    theme="snow"
                    placeholder="Describe your card..."
                    value={getValues('description')}
                    onChange={(value) => setValue('description', value)}
                  />
                </div>
                <div className="flex flex-row gap-4">
                  <button className="px-3 py-1 bg-blue-600 text-white rounded-lg" type="submit">
                    Save
                  </button>
                  <button
                    className="px-3 py-1 hover:bg-slate-200 text-black rounded-lg"
                    onClick={() => setIsShowRichTextEditor(false)}
                  >
                    Cancel
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
        <div className="col-span-12 lg:col-span-4">
          <div className="flex flex-row my-2 gap-4">
            <select
              value={columnId}
              className="bg-green-500 p-2 text-white"
              onChange={(e) => handleMoveCardToAnotherColumn(parseInt(e.target.value))}
            >
              {columns &&
                columns.map((column) => (
                  <option key={column.id} value={column.id}>
                    {column.title.toUpperCase()}
                  </option>
                ))}
            </select>
          </div>
          <div className=" flex flex-col justify-start items-start border border-slate-300">
            <h2 className="border-b-2 w-full py-4 text-left px-2">Details</h2>
            <div className="grid grid-cols-12 items-center w-full my-2">
              <div className="col-span-5 text-left mx-2">Assignee</div>
              <div className="col-span-7 relative">
                <div
                  className="flex flex-row items-center gap-4"
                  onClick={() => setIsOpenMenuAssignUser(true)}
                >
                  <div
                    className="p-2 bg-yellow-400"
                    title={
                      card.assigned_user ? card.assigned_user.username.slice(0, 2) : 'unassigned'
                    }
                  >
                    {card.assigned_user ? card.assigned_user.username.slice(0, 2) : '#'}
                  </div>
                  <div>{card.assigned_user ? card.assigned_user.username : 'Unassigned'}</div>
                </div>
                {isOpenMenuAssignUser && (
                  <MenuAssignUser
                    boardId={boardId}
                    isOpen={isOpenMenuAssignUser}
                    setIsOpen={setIsOpenMenuAssignUser}
                    card={card}
                  />
                )}
              </div>
              {!card.assigned_user && (
                <div
                  className="col-span-12 grid grid-cols-12 text-left mx-4 py-2 text-blue-600"
                  onClick={handleAssignToMe}
                >
                  <span className="col-span-5"></span>
                  <span className="col-span-7">Assign to me</span>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </dialog>
  );
};

export default DialogDetailCard;
