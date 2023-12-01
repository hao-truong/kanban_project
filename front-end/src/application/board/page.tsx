import { Plus } from "lucide-react";
import KanbanColumn from "../home/KanbanColumn";
import { useEffect, useState } from "react";
import useCheckLogin from "@/shared/hooks/useCheckLogin";
import { useNavigate, useParams } from "react-router-dom";
import { toast } from "react-toastify";
import BoardService from "@/shared/services/BoardService";



const BoardPage = () => {
    const isLogin = useCheckLogin();
    const navigate = useNavigate();
    const params = useParams<{ boardId: string }>();
    const [board, setBoard]= useState<Board | null>(null);

    useEffect(() => {
        if(!isLogin) {
            navigate("/auth/sign-in");
        }
    }, [isLogin])

    useEffect(() => {
        const getBoard = async (boardId: number) => {
            try {
                const {data} = await BoardService.getBoard(boardId);
                setBoard(data);
            } catch (error: any) {
                toast.error(error.message);
            }
        }

        if(params.boardId) {
            getBoard(Number.parseInt(params.boardId))
        }
    }, [params])

    const createColumn = async () => {
    };

    return (
        <div>
            <div className="flex flex-row justify-between my-10">
                <h2 className="uppercase">{board?.title}</h2>
                <button className="flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400" onClick={createColumn}>
                    <Plus />
                    <span>Create column</span>
                </button>
            </div>
            <div className="flex flex-row gap-4 overflow-auto">
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
            </div>
        </div>

    )
}

export default BoardPage;