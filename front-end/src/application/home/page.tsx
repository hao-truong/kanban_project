import { useEffect, useState } from "react";
import KanbanBoard from "./KanbanBoard";
import { toast } from "react-toastify";
import BoardService from "@/shared/services/BoardService";
import { InputBase } from "@mui/material";
import { Plus, Search } from "lucide-react";
import DialogCreateBoard from "./DialogCreateBoard";

const HomePage = () => {
    const [boards, setBoards] = useState<Board[]>([]);
    const [isOpenDialogCreateBoard, setIsOpenDialogCreateBoard] = useState<boolean>(false);

    useEffect(() => {
        const getBoards = async () => {
            try {
                const { data } = await BoardService.getMyBoards();

                setBoards(data);
            } catch (error: any) {
                toast.error(error.message);
            }
        }

        getBoards();
    }, [])

    return (
        <div className="">
            <h2 className="w-full text-center font-bold text-5xl my-10">YOUR BOARDS</h2>
            <div className="flex flex-row justify-between items-center">
                <div className="flex flex-row items-center gap-4 w-fit my-5 bg-slate-100 px-4">
                    <InputBase
                        className="py-3"
                        sx={{ ml: 1, flex: 1 }}
                        placeholder="Search Google Maps"
                        inputProps={{ 'aria-label': 'search google maps' }}
                    />
                    <Search className="cursor-pointer" size={25} />
                </div>
                <div className="h-fit flex flex-row items-center gap-4 px-4 py-2 cursor-pointer hover:bg-slate-400" onClick={() => setIsOpenDialogCreateBoard(!isOpenDialogCreateBoard)}>
                    <Plus />
                    <span>Create board</span>
                    <DialogCreateBoard isOpen={isOpenDialogCreateBoard} setIsOpen={setIsOpenDialogCreateBoard} setBoards={setBoards} />
                </div>
            </div>
            <div className="grid grid-cols-4 gap-4">
                {
                    boards.map((board) => (
                        <KanbanBoard board={board} key={board.id} />
                    ))
                }
            </div>
        </div>
    )
}

export default HomePage;
