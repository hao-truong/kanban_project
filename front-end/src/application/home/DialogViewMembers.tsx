import BoardService from "@/shared/services/BoardService";
import { useGlobalState } from "@/shared/storages/GlobalStorage";
import { Crown, Sparkle } from "lucide-react";
import { useEffect, useRef, useState } from "react";
import { toast } from "react-toastify";

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
    }, [user])

    return (
        <div className={`flex flex-row justify-between items-center w-full min-w-[200px]`}>
            <div className="flex flex-row gap-4">
                <strong >{orderNumber}</strong>
                {isMe && <Sparkle color="red" />}
                {isCreator && <Crown color='blue' />}
            </div>
            <span>{member.username}</span>
        </div>
    )
}

interface itemProps {
    isOpen: boolean;
    setIsOpen: Function;
    board: Board;
}
const DialogViewMembers = ({ board, isOpen, setIsOpen }: itemProps) => {
    const [members, setMembers] = useState<User[]>([]);
    const dialogRef = useRef<HTMLDialogElement | null>(null);
    const bodyDialogRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        const getMembers = async () => {
            try {
                const { data } = await BoardService.getMembers(board.id);

                setMembers(data);
            } catch (error: any) {
                toast.error(error.message);
            }
        }

        getMembers();
    }, [])

    useEffect(() => {
        if (isOpen && dialogRef) {
            dialogRef.current?.showModal();
        }

        const handleOutsideClick = (event: any) => {
            if (dialogRef.current && bodyDialogRef.current && !bodyDialogRef.current.contains(event.target)) {
                dialogRef.current?.close();
                setIsOpen(false);
            }
        };

        document.addEventListener("mousedown", handleOutsideClick);

        return () => {
            document.removeEventListener("mousedown", handleOutsideClick);
        };
    }, [isOpen, dialogRef, bodyDialogRef])

    return (
        <div>
            <dialog ref={dialogRef} className=" rounded-lg p-10">
                <div ref={bodyDialogRef} className="flex flex-col items-end justify-center gap-4">
                    {
                        members.map((member, _index) => (
                            <MemberComponent member={member} orderNumber={_index + 1} key={member.id} creatorId={board.creator_id} />
                        ))
                    }
                </div>
            </dialog>
        </div>
    )
}

export default DialogViewMembers;