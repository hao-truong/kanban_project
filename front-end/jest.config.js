export default {
  preset: 'ts-jest',
  testEnvironment: 'jsdom',
  transform: {
    '^.+\\.tsx?$': [
      'ts-jest',
      {
        useESM: true,
      },
    ],
  },
  rootDir: 'src',
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/$1',
  },
};
