import { useEffect, useRef } from 'react';

interface itemProps {
  card: Card;
  isOpen: boolean;
  setIsOpen: Function;
}

const DialogDetailCard = ({ card, isOpen, setIsOpen }: itemProps) => {
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);

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
  }, [isOpen, dialogRef]);

  return (
    <dialog ref={dialogRef} className="rounded-lg p-10">
      <div ref={bodyDialogRef} className="grid grid-cols-12">
        <div className="col-span-8">
          <div>{card.title}</div>
        </div>
        <div className="col-span-4">
          <h2>Details</h2>
          <div className="grid grid-cols-12">
            <div className="col-span-5">Assignee</div>
            <div className="col-span-7">
              {card.assigned_user ? card.assigned_user.username : 'Unassigned'}
            </div>
          </div>
        </div>
      </div>
    </dialog>
  );
};

export default DialogDetailCard;
