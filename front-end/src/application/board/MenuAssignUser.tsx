import CardService from '@/shared/services/CardService';
import { getMembers } from '@/shared/services/QueryService';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import Helper from '@/shared/utils/helper';
import { Sparkle } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useQuery, useQueryClient } from 'react-query';
import { toast } from 'react-toastify';

interface itemProps {
  boardId: number;
  isOpen: boolean;
  setIsOpen: Function;
  card: Card;
}

interface memberProps {
  member: User;
  paramsApi: ParamsApiCard;
  setIsOpen: Function;
  assignedUser: User | null;
}

const MemberComponent = ({ member, paramsApi, setIsOpen, assignedUser }: memberProps) => {
  const queryClient = useQueryClient();
  const { user } = useGlobalState();
  const [isMe, setIsMe] = useState<boolean>(false);
  const [isAssignedUser, setIsAssignedUser] = useState<boolean>(false);

  useEffect(() => {
    if (user && user.id === member.id) {
      setIsMe(true);
    }
  }, [user]);

  useEffect(() => {
    if (assignedUser && assignedUser.id === member.id) {
      setIsAssignedUser(true);
    }
  }, [assignedUser]);

  const handleAssignUser = async () => {
    if (isMe) {
      const data = await CardService.assignMe(paramsApi)
        .then((response) => response.data)
        .catch((responseError: ResponseError) => toast.error(responseError.message));

      queryClient.invalidateQueries(`getCards${paramsApi.columnId}`);
      setIsOpen(false);
      toast.success(data);
      return;
    }

    const data = await CardService.assignToMember(paramsApi, {
      assignToMemberId: member.id,
    })
      .then((response) => response.data)
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    queryClient.invalidateQueries(`getCards${paramsApi.columnId}`);
    setIsOpen(false);
    toast.success(data);
  };

  return (
    <div
      className={`p-2 hover:bg-slate-400 hover:text-white flex flex-row items-center gap-4 ${
        isAssignedUser ? 'bg-red-500 text-white' : ''
      }`}
      onClick={(e) => {
        e.stopPropagation();
        handleAssignUser();
      }}
    >
      <span className="text-left">{member.username}</span>
      {isMe && <Sparkle color="red" />}
    </div>
  );
};

const MenuAssignUser = ({ boardId, isOpen, setIsOpen, card }: itemProps) => {
  const menuRef = useRef<HTMLUListElement | null>(null);
  const { data: members } = useQuery<User[]>('getMembersOfBoard', () => getMembers(boardId), {
    enabled: !!boardId,
  });

  useEffect(() => {
    Helper.handleOutSideClick(menuRef, setIsOpen);
  }, [isOpen, menuRef]);

  return (
    <ul ref={menuRef} className="absolute bg-white top-full right-0 py-1 border border-black z-50">
      {members &&
        members.map((member) => (
          <MemberComponent
            member={member}
            key={member.id}
            paramsApi={{
              cardId: card.id,
              columnId: card.column_id,
              boardId,
            }}
            assignedUser={card.assigned_user}
            setIsOpen={setIsOpen}
          />
        ))}
    </ul>
  );
};

export default MenuAssignUser;
