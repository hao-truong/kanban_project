import BoardService from "@/shared/services/BoardService";
import { MAX_LENGTH_INPUT_STRING, MIN_LENGTH_INPUT_STRING } from "@/shared/utils/constant";
import { yupResolver } from "@hookform/resolvers/yup";
import { TextField } from "@mui/material";
import { useEffect, useRef } from "react";
import { SubmitHandler, useForm } from "react-hook-form";
import { toast } from "react-toastify";
import * as yup from "yup";

interface itemProps {
    isOpen: boolean;
    setIsOpen: Function;
    setBoards: Function;
}

const schemaValidation = yup
    .object({
        title: yup
            .string()
            .required("Title is required")
            .min(
                MIN_LENGTH_INPUT_STRING,
                `Title must be at least ${MIN_LENGTH_INPUT_STRING} characters long`
            )
            .max(
                MAX_LENGTH_INPUT_STRING,
                `Title must be at least ${MAX_LENGTH_INPUT_STRING} characters long`
            ),
    })
    .required();

const DialogCreateBoard = ({ isOpen, setIsOpen, setBoards }: itemProps) => {
    const dialogRef = useRef<HTMLDialogElement | null>(null);
    const bodyDialogRef = useRef<HTMLDivElement | null>(null);
    const {
        register,
        handleSubmit,
        reset,
        formState: { errors },
    } = useForm<BoardReq>({
        resolver: yupResolver(schemaValidation),
    });
    const onSubmit: SubmitHandler<BoardReq> = async (boardReqData) => {
        try {
            const { data } = await BoardService.createBoard(boardReqData);

            toast.success("Create new board successfully!");
            setBoards((preBoards: Board[]) => [...preBoards, data]);
            reset();
            if (dialogRef.current) {
                dialogRef.current.close();
            }
        } catch (error: any) {
            toast.error(error.message);
        }
    };

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
        <dialog ref={dialogRef} className=" rounded-lg p-10">
            <div ref={bodyDialogRef} className="flex flex-col items-end justify-center gap-4">
                <form className="min-w-[500px] text-center flex flex-col gap-5 rounded-xl" onSubmit={handleSubmit(onSubmit)}>
                    <TextField id="outlined-basic" label="Title board" variant="outlined" {...register('title')} error={errors.title ? true : false}
                        helperText={errors.title?.message} />
                    <div className="flex flex-row justify-end">
                        <button className="w-fit py-2 px-6 bg-blue-500 text-white rounded-lg" type="submit">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    )
}

export default DialogCreateBoard;