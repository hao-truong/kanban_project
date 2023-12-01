import BoardService from "@/shared/services/BoardService";
import { MAX_LENGTH_INPUT_STRING, MAX_TITLE_LENGTH, MIN_LENGTH_INPUT_STRING } from "@/shared/utils/constant";
import Helper from "@/shared/utils/helper";
import { yupResolver } from "@hookform/resolvers/yup";
import { OutlinedInput } from "@mui/material";
import { ArrowRightSquare, Check, MoreHorizontal, X } from "lucide-react";
import { useEffect, useRef, useState } from "react";
import { SubmitHandler, useForm } from "react-hook-form";
import { toast } from "react-toastify";
import * as yup from "yup";
import { useQueryClient } from 'react-query';
import { Link } from "react-router-dom";

interface itemProps {
    board: Board;
}

const schemaValidation = yup
    .object({
        title: yup.string().required("Username is required!")
            .min(
                MIN_LENGTH_INPUT_STRING,
                `Password must be at least ${MIN_LENGTH_INPUT_STRING} characters long`
            )
            .max(
                MAX_LENGTH_INPUT_STRING,
                `Password must be at least ${MAX_LENGTH_INPUT_STRING} characters long`
            ),
    })
    .required();

const KanbanBoard = ({ board }: itemProps) => {
    const queryClient = useQueryClient();
    const [isClickTilte, setIsClickTitle] = useState<boolean>(false);
    const [isHoverTitle, setIsHoverTitle] = useState<boolean>(false);
    const [isShowMenu, setIsShowMenu] = useState<boolean>(false);
    const titleRef = useRef<HTMLFormElement | null>(null);
    const {
        register,
        handleSubmit,
        formState: { errors },
        setValue
    } = useForm<BoardReq>({
        resolver: yupResolver(schemaValidation),
    });
    const onSubmit: SubmitHandler<BoardReq> = async (reqData) => {
        try {
            await BoardService.updateBoard(board.id, reqData);

            queryClient.invalidateQueries('getMyBoards');
            setIsClickTitle(false);
        } catch (error: any) {
            toast.error(error.message);
        }
    };

    useEffect(() => {
        setValue('title', board.title);
    }, [board])

    useEffect(() => {
        if (!isClickTilte) {
            setValue('title', board.title);
        }

        if (!isHoverTitle) {
            setIsShowMenu(false);
        }
    }, [isClickTilte, isHoverTitle])

    const handleDeleteBoard = async () => {
        try {
            const { data } = await BoardService.deleteBoard(board.id);

            queryClient.invalidateQueries('getMyBoards');
            toast.success(data);
        } catch (error: any) {
            toast.error(error.message);
        }
    }

    return (
        <div className="bg-slate-200 w-full p-4 text-center flex flex-col gap-5 rounded-xl">
            <div
                onMouseOver={() => setIsHoverTitle(true)}
                onMouseLeave={() => setIsHoverTitle(false)}
            >
                {
                    isClickTilte &&
                    <form ref={titleRef} className="relative" onSubmit={handleSubmit(onSubmit)}>
                        <OutlinedInput
                            autoFocus
                            className="w-full bg-white"
                            onFocus={() => Helper.handleOutSideClick(titleRef, setIsClickTitle)}
                            id="outlined-adornment-weight"
                            aria-describedby="outlined-weight-helper-text"
                            {...register('title')}
                            error={errors.title ? true : false}
                            inputProps={{ maxLength: MAX_TITLE_LENGTH }}
                        />
                        <div className="flex flex-row justify-end gap-2 mt-2 absolute right-0">
                            <button type="submit">
                                <Check size={40} className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer" />
                            </button>
                            <X size={40} className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer" onClick={() => setIsClickTitle(false)} />
                        </div>
                    </form>

                }
                {
                    !isClickTilte &&
                    <div className="flex flex-row justify-between items-center gap-4">
                        <h2 className="w-full uppercase text-xl font-bold py-2 hover:bg-white cursor-pointer text-left" onClick={() => setIsClickTitle(true)}>{board.title}</h2>
                        {
                            isHoverTitle &&
                            (
                                <div className="relative">
                                    <MoreHorizontal className="cursor-pointer hover:bg-slate-100 p-2" size={40} onClick={() => setIsShowMenu(!isShowMenu)} />
                                    {
                                        isShowMenu &&
                                        <ul className="absolute bg-white top-full right-0 py-1">
                                            <li className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer" onClick={handleDeleteBoard}>Delete</li>
                                        </ul>
                                    }
                                </div>
                            )
                        }
                    </div>
                }
            </div>
            <div className="flex flex-row justify-between"><strong className="text-red-700">Created At:</strong> <span>{Helper.formatDate(board.created_at)}</span></div>
            <div className="flex flex-row justify-between"><strong className="text-red-700">Updated At:</strong> <span>{Helper.formatDate(board.updated_at)}</span></div>
            <div>
                <Link to={`/boards/${board.id}`} className="flex w-full flex-row justify-end ">
                    <ArrowRightSquare size={40} className="p-1 hover:bg-white rounded-lg"/>
                </Link>
            </div>
        </div>
    )
}

export default KanbanBoard;