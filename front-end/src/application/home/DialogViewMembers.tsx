import { getMembers } from '@/shared/services/QueryService';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import { Crown, Sparkle } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useQuery } from 'react-query';

interface memberProps {
  member: User;
  orderNumber: number;
  creatorId: number;
}

const MemberComponent = ({ member, orderNumber, creatorId }: memberProps) => {
  const { user } = useGlobalState();
  const [isMe, setIsMe] = useState<boolean>(false);
  const [isCreator, setIsCreator] = useState<boolean>(false);

  useEffect(() => {
    if (user?.id === member.id) {
      setIsMe(true);
    }

    if (member?.id === creatorId) {
      setIsCreator(true);
    }
  }, [user]);

  return (
    <div className={`grid grid-cols-3 w-full min-w-[500px]`}>
      <div className="flex flex-row gap-4">
        <strong>{orderNumber}</strong>
        {isMe && <Sparkle color="red" />}
        {isCreator && <Crown color="blue" />}
      </div>
      <span className="text-left">{member.username}</span>
      <span className="text-left">{member.alias}</span>
    </div>
  );
};

interface itemProps {
  isOpen: boolean;
  setIsOpen: Function;
  board: Board;
}

const DialogViewMembers = ({ board, isOpen, setIsOpen }: itemProps) => {
  const dialogRef = useRef<HTMLDialogElement | null>(null);
  const bodyDialogRef = useRef<HTMLDivElement | null>(null);
  const { data: members } = useQuery<User[]>('getMembersOfBoard', () => getMembers(board!.id), {
    enabled: !!board,
  });

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

  return (
    <div>
      <dialog ref={dialogRef} className=" rounded-lg p-10">
        <div ref={bodyDialogRef} className="flex flex-col items-end justify-center gap-4">
          {members &&
            members.map((member, _index) => (
              <MemberComponent
                member={member}
                orderNumber={_index + 1}
                key={member.id}
                creatorId={board.creator_id}
              />
            ))}
        </div>
      </dialog>
    </div>
  );
};

export default DialogViewMembers;
