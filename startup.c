#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

int main() {
    FILE *fp;
    int i=1;
    while (1) {
         system ("/usr/bin/php /mnt0/stud/mcquade3/PUBLIC_HTML/CSC310/continuous.php > /dev/null &");
         i++;
         fp = fopen("/tmp/mikem","w");
         fprintf(fp, "%d", i);
         fclose(fp);
         sleep(5);
    }
}
