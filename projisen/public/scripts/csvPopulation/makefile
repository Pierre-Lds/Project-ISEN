MYSQL_CONCPP_DIR= -I/usr/include/cppconn
LDLIBS = -lmysqlcppconn
CPPFLAGS = $(MYSQL_CONCPP_DIR) -L /usr/lib $(LDLIBS)


NAME = out

SRC =     main.cpp \
			randomPopulate/studentPopulation.cpp	\
			randomPopulate/staffPopulation.cpp		\
			exportToCSV/exportToCSV.cpp	\
			populateWithCSV/populateFromCSV.cpp	\



OBJ = $(SRC:.cpp=.o)
CC = g++

CFLAGS = -Wall -Wextra -Wshadow

all: $(NAME)

$(NAME): $(OBJ)
		 $(CC) -o $(NAME) $(OBJ) $(CPPFLAGS)

clean:
		$(RM) $(OBJ)

fclean: clean
		$(RM) $(NAME)

re: fclean all

.PHONY: all clean fclean re
