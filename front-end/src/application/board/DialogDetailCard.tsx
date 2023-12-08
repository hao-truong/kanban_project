import CardService from '@/shared/services/CardService';
import { X } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useQuery, useQueryClient } from 'react-query';
import ReactQuill from 'react-quill';
import { toast } from 'react-toastify';
import MenuAssignUser from './MenuAssignUser';
import { getColumnsOfBoard } from '@/shared/services/QueryService';

interface itemProps {
  card: Card;
  isOpen: boolean;
  setIsOpen: Function;
  boardId: number;
}

const DialogDetailCard = ({ card, isOpen, setIsOpen, boardId }: itemProps) => {
  const queryClient = useQueryClient();
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);
  const [isShowRichTextEditor, setIsShowRichTextEditor] = useState<boolean>(false);
  const [isOpenMenuAssignUser, setIsOpenMenuAssignUser] = useState<boolean>(false);
  const [columnId, setColumnId] = useState<number>(card.column_id);
  const { data: columns } = useQuery<Column[]>(
    'getColumnsOfBoard',
    () => getColumnsOfBoard(boardId),
    {
      enabled: !!boardId,
    },
  );

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

  const handleAssignToMe = async () => {
    const data = await CardService.assignMe({
      cardId: card.id,
      columnId: card.column_id,
      boardId,
    })
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    queryClient.invalidateQueries(`getCards${card.column_id}`);
    setIsOpen(false);
    toast.success(data);
  };

  const handleMoveCardToAnotherColumn = async (selectedColumnId: number) => {
    setColumnId(selectedColumnId);
    const data = await CardService.changeColumnForCard(
      {
        columnId: card.column_id,
        cardId: card.id,
        boardId,
      },
      { destinationColumnId: selectedColumnId },
    )
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    if (data) {
      queryClient.invalidateQueries(`getCards${card.column_id}`);
      queryClient.invalidateQueries(`getCards${selectedColumnId}`);
    }
  };

  return (
    <dialog
      ref={dialogRef}
      className="rounded-lg p-10 min-w-[80%] overflow-y-visible min-h-[400px]"
    >
      <div ref={bodyDialogRef} className="grid grid-cols-12 gap-4">
        <div className="col-span-8 flex flex-col items-start text-left gap-4">
          <div className="text-3xl">{card.title}</div>
          <div className="w-full flex flex-col gap-2">
            <strong>Description</strong>
            {!isShowRichTextEditor && (
              <div
                className="text-slate-500 py-2 hover:bg-slate-500 hover:text-white cursor-text"
                onClick={() => setIsShowRichTextEditor(true)}
              >
                {card.description === '' ? 'Add a description...' : card.description}
              </div>
            )}
            {isShowRichTextEditor && (
              <div className="flex flex-col gap-4">
                <div>
                  <ReactQuill
                    theme="snow"
                    value={card.description}
                    placeholder="Describe your card..."
                  />
                </div>
                <div className="flex flex-row gap-4">
                  <button className="px-3 py-1 bg-blue-600 text-white rounded-lg">Save</button>
                  <button
                    className="px-3 py-1 hover:bg-slate-200 text-black rounded-lg"
                    onClick={() => setIsShowRichTextEditor(false)}
                  >
                    Cancel
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
        <div className="col-span-4">
          <div className="flex flex-row justify-end my-2">
            <div className="p-1 hover:bg-slate-400" onClick={() => setIsOpen(false)}>
              <X size={40} />
            </div>
          </div>
          <div className="flex flex-row my-2">
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
                  className="col-span-12 text-right mx-4 py-2 text-blue-600"
                  onClick={handleAssignToMe}
                >
                  Assign to me
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
