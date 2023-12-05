import { Plus } from "lucide-react";
import KanbanColumn from "./KanbanColumn";
import { useEffect, useState } from "react";
import useCheckLogin from "@/shared/hooks/useCheckLogin";
import { useNavigate, useParams } from "react-router-dom";
import { toast } from "react-toastify";
import BoardService from "@/shared/services/BoardService";
import { useQuery, useQueryClient } from 'react-query';
import ColumnService from "@/shared/services/ColumnService";
import DialogCreateColumn from "./DialogCreateColumn";

const getColumnsOfBoard = async (boardId: number): Promise<Column[]> => {
    try {
        const { data } = await ColumnService.getColumnsOfBoard(boardId);
        return data;
    } catch (error: any) {
        toast.error(error.message);
        return [];
    }
};

const getBoard = async (boardId: number): Promise<Board | null> => {
    try {
        const { data } = await BoardService.getBoard(boardId);
        return data;
    } catch (error: any) {
        toast.error(error.message);
        return null;
    }
}

const BoardPage = () => {
    useQueryClient()
    const isLogin = useCheckLogin();
    const navigate = useNavigate();
    const params = useParams<{ boardId: string }>();
    const [isShowDialogCreateColumn, setIsShowDialogCreateColumn] = useState<boolean>(false);
    const { data: columns } = useQuery<Column[]>('getColumnsOfBoard', () => getColumnsOfBoard(Number(params.boardId)), {
        enabled: !!params.boardId
    });
    const {data: board} = useQuery<Board | null>('getBoard', () => getBoard(Number(params.boardId)), {
        enabled: !!params.boardId
    });

    useEffect(() => {
        if (!isLogin) {
            navigate("/auth/sign-in");
        }
    }, [isLogin])

    return (
        <div>
            <div className="flex flex-row justify-between my-10">
                <h2 className="uppercase">{board?.title}</h2>
                <button className="flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400" onClick={() => setIsShowDialogCreateColumn(!isShowDialogCreateColumn)}>
                    <Plus />
                    <span>Create column</span>
                </button>
                {
                    board &&
                    <DialogCreateColumn isOpen={isShowDialogCreateColumn} setIsOpen={setIsShowDialogCreateColumn} boardId={board.id} />
                }
            </div>
            <div className="flex flex-row gap-4 overflow-auto">
                {
                    columns && columns.length !== 0 && columns.map((column) => (
                        <KanbanColumn column={column} key={column.id} />
                    ))
                }
            </div>
            {
                columns?.length === 0 &&
                (
                    <div className="text-center text-xl">Don't have any column in this board.</div>
                )
            }
        </div>

    )
}

export default BoardPage;