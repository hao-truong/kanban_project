import AuthService from "@/shared/services/AuthService";
import {
    MAX_LENGTH_INPUT_STRING,
    MAX_LENGTH_PASSWORD,
    MIN_LENGTH_INPUT_STRING,
    MIN_LENGTH_PASSWORD,
} from "@/shared/utils/constant";
import { yupResolver } from "@hookform/resolvers/yup";
import { Button, TextField } from "@mui/material";
import { SubmitHandler, useForm } from "react-hook-form";
import { Link, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import * as yup from "yup";

interface IFormInput {
    username: string;
    password: string;
    confirmPassword: string;
    alias: string;
    email: string;
}

const schemaValidation = yup
    .object({
        email: yup.string().required("Email is required!").matches(
            /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i,
            'Invalid email!'
        ),
        password: yup
            .string()
            .required("Password is required")
            .min(
                MIN_LENGTH_PASSWORD,
                `Password must be at least ${MIN_LENGTH_PASSWORD} characters long`
            )
            .max(
                MAX_LENGTH_PASSWORD,
                `Password must be at least ${MAX_LENGTH_PASSWORD} characters long`
            ),
        confirmPassword: yup
            .string()
            .required("Confirm password is required")
            .min(
                MIN_LENGTH_PASSWORD,
                `Confirm password must be at least ${MIN_LENGTH_PASSWORD} characters long`
            )
            .max(
                MAX_LENGTH_PASSWORD,
                `Confirm password must be at least ${MAX_LENGTH_PASSWORD} characters long`
            )
            .oneOf([yup.ref("password"), ""], "Confirm password must match"),
        alias: yup
            .string()
            .required("Alias name is required")
            .min(
                MIN_LENGTH_INPUT_STRING,
                `Alias name must be at least ${MIN_LENGTH_INPUT_STRING} characters long`
            )
            .max(
                MAX_LENGTH_INPUT_STRING,
                `Alias name must be at least ${MAX_LENGTH_INPUT_STRING} characters long`
            ),
        username: yup.string().required("Username is required").min(
            MIN_LENGTH_INPUT_STRING,
            `Username must be at least ${MIN_LENGTH_INPUT_STRING} characters long`
        )
            .max(
                MAX_LENGTH_INPUT_STRING,
                `Username must be at least ${MAX_LENGTH_INPUT_STRING} characters long`
            ),
    })
    .required();

const RegisterForm = () => {
    const navigate = useNavigate();
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<IFormInput>({
        resolver: yupResolver(schemaValidation),
    });
    const onSubmit: SubmitHandler<IFormInput> = async (registerReqData) => {
        try {
            const { data } = await AuthService.register({ ...registerReqData });

            toast.success(data);
            navigate("/auth/sign-in");
        } catch (error: any) {
            toast.error(error.message);
        }
    };

    return (
        <div className="fixed top-1/2 left-1/2 m-auto p-10 -translate-x-1/2 -translate-y-1/2 border border-slate-400 rounded-xl">
            <h1 className="text-center uppercase text-xl font-bold">Register</h1>
            <form
                onSubmit={handleSubmit(onSubmit)}
                className="flex flex-col gap-5 my-10"
            >
                <TextField
                    label="Username"
                    variant="outlined"
                    {...register("username")}
                    className="w-[400px]"
                    error={errors.username ? true : false}
                    helperText={errors.username?.message}
                />
                <TextField
                    label="Email"
                    variant="outlined"
                    {...register("email")}
                    className="w-[400px]"
                    error={errors.email ? true : false}
                    helperText={errors.email?.message}
                />
                <TextField
                    label="Alias name"
                    variant="outlined"
                    {...register("alias")}
                    className="w-[400px]"
                    error={errors.alias ? true : false}
                    helperText={errors.alias?.message}
                />
                <TextField
                    label="Password"
                    variant="outlined"
                    type="password"
                    {...register("password")}
                    className="w-[400px]"
                    error={errors.password ? true : false}
                    helperText={errors.password?.message}
                />
                <TextField
                    label="Confirm password"
                    variant="outlined"
                    type="password"
                    {...register("confirmPassword")}
                    className="w-[400px]"
                    error={errors.confirmPassword ? true : false}
                    helperText={errors.confirmPassword?.message}
                />
                <div className="flex justify-end">
                    <Button variant="contained" type="submit" className="w-fit">
                        Register
                    </Button>
                </div>
            </form>
            <div>
                <span>Do you already have an account?</span>
                <Link className="text-blue-400 ml-2" to={"/auth/sign-in"}>
                    Sign In
                </Link>
            </div>
        </div>
    );
};

export default RegisterForm;