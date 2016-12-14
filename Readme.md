# CODE RUNNER

Version: 3.0 January 2016

Author: Richard Lobb, University of Canterbury, New Zealand.

NOTE: this documentation is available in a more-easily browsed form,
together with a sample quiz containing a few CodeRunner questions, at
[coderunner.org.nz](http://coderunner.org.nz).

## Introduction

CodeRunner is a Moodle question type that requests students to submit program code
to some given specification. The submission is graded by running a series of tests on
the code in a sandbox, comparing the output with the expected output.
A trivial example might be a Python function *sqr(x)* that returns its
parameter squared, but there is essentially no limit on the complexity of
questions that can be asked.

CodeRunner is intended to be run in an adaptive mode, so that students know
immediately if their code is passing the tests. In the typical
'all-or-nothing' mode, all test cases must pass
if the submission is to be awarded any marks. The mark for a set of questions
in a quiz is then determined primarily by which questions the student is able
to  solve successfully and then secondarily by how many submissions the student
makes on each question. However, it is also possible to run CodeRunner questions
in a traditional quiz mode where the mark is determined by how many of the tests
the code successfully passes.

CodeRunner and its predecessors *pycode* and *ccode* has been in use at the
University of Canterbury for about five years, running many hundreds of
thousands of
student quiz submissions in Python, C , Octave and Matlab. Laboratory work,
assignment work and mid-semester tests in the
introductory first year Python programming course (COSC121), which has around
400 students
in the first semester and 200 in the second, are all assessed using CodeRunner
questions. The final exams for COSC121 have also been run
using Moodle/CodeRunner since November 2014.

The second year C course (ENCE260) of around 200 students makes similar
use of CodeRunner
using C questions and a third year Civil Engineering course (ENCN305),
taught in Matlab,
uses CodeRunner for all labs and for the mid-semester programming exam. Other
courses using Moodle/CodeRunner include:

1. EMTH171 Mathematical Modelling and Computation
1. COSC261 Formal Languages and Compilers 
1. COSC367 Computational Intelligence
1. ENCE360 Operating Systems
1. SENG365 Web Computing Architectures

CodeRunner currently supports Python2 (considered obsolescent), Python3,
C, Java, PHP5, JavaScript (NodeJS), Octave and Matlab. C++ questions are
not built-in but can be easily supported by custom question types.
The architecture allows
easy extension to other languages.

For security and load reasons, it is recommended that CodeRunner be set up
on a special quiz-server rather than on an institution-wide Moodle server.
However, CodeRunner does allow use of a remote
sandbox machine for running all student-submitted code so provided only
that sandbox is enabled, as discussed below, this version should actually
be safe to install
on an institutional server.

A single 4-core server can handle an average quiz question submission rate of
about 60 quiz questions per minute while maintaining a response time of less
than about 3 - 4 seconds, assuming the student code itself runs in a
fraction of a second. We have run CodeRunner-based exams with around 250 students
and experienced only light to moderate load factors on an 8-core Moodle
server. The Jobe server, which runs student submissions (see below),
is even more lightly loaded during such an exam.

The CodeRunner question type can be installed on any modern Moodle system
(version 2.6 or later including version 3.0), on Linux, Windows and Mac. For security reasons
submitted jobs are usually run on a separate machine called the "Jobe server"
or "Jobe sandbox machine".

## Installation

This chapter describes how to install CodeRunner. It assumes the
existence of a working Moodle system, version 2.6 or later (including
Moodle 3).

If you are installing for the first time, jump straight to section 2.2.

### Upgrading from an earlier version

IMPORTANT: this version of CodeRunner is incompatible with versions prior to
2.4.0. If you're attempting to upgrade from an earlier version, you should
first upgrade to the most recent version 2 (checkout branch V2 in the repository).

If you are already running CodeRunner version 2.4.0 or later, you can upgrade
simply by following the instructions in the next section.

Note that all existing questions in the system `CR_PROTOTYPES` category with names containing the
string `PROTOTYPE_` are deleted by the installer, which then re-loads them
from the file

    db/questions-CR_PROTOTYPES.xml

Hence if you have developed your own question prototypes and placed them in
the system CR\_PROTOTYPES category you must export them in Moodle XML format before
upgrading. You can if you wish place that exported file in the 'db' directory
with a name ending in `_PROTOTYPES.xml`; they will then be automatically
loaded by the installer. Alternatively you can import them at your leisure
later on using the usual question-bank import function in the
web interface.

### Installing CodeRunner from scratch

To conform to Moodle standards, the CodeRunner question type is now in two
different github repositories: github.com/trampgeek/moodle-qbehaviour_adaptive_adapted_for_coderunner
and github.com/trampgeek/moodle-qtype_coderunner. Install the question behaviour
first (see the instructions within that repository), then the question type second.

To install the question type, either:

1. Download the zip file of the required branch from github,
unzip it into the directory `moodle/question/type` and change the name
of the newly-created directory from `moodle-qtype_coderunner-<branchname>` to just
`coderunner`.

1. Get the code using git by running the following command in the
top level folder of your Moodle install:

        git clone git://github.com/trampgeek/moodle-qtype_coderunner.git question/type/coderunner

Whichever of the two methods you used, you will also need to change the ownership
and access rights to ensure the directory and
its contents are readable by the webserver.

Having carried out one of the above methods,
if you have local question prototypes to add to the built-in prototype set you
should now
copy them into the `<moodlehome>/question/type/coderunner/db` folder.
They should be
Moodle XML file(s) with names ending in `_PROTOTYPES.xml` (case-sensitive).
[If you don't understand what this paragraph means, then it probably
doesn't concern you ... move on.]

After carrying out one of the above install methods, you can complete the
installation by logging onto the server through the web interface as an
administrator and following the prompts to upgrade the database as appropriate.

In its initial configuration, CodeRunner is set to use a University of
Canterbury [Jobe server](https://github.com/trampgeek/jobe) to run jobs. You are
welcome to use this during initial testing, but it is
not intended for production use. Authentication and authorisation
on that server is
via an API-key and the default API-key given with CodeRunner imposes
a limit of 100
per hour over all clients using that key. If you decide that CodeRunner is
useful to you, *please* set up your own Jobe sandbox as 
described in *Sandbox configuration* below. Alternatively, if you wish to
continue to use our Jobe server, you can apply to the
[developer](mailto://trampgeek@gmail.org) for your own
API key, stating how long you will need to use the key and a reasonable
upper bound on the number of jobs you will need to submit per hour. We
will do our best to accommodate you if we have sufficient capacity.

WARNING: at least a couple of users have broken CodeRunner by duplicating
the prototype questions in the System/CR_PROTOTYPES category. `Do not` touch
those special questions until you have read this entire manual and
are familiar with the inner workings of CodeRunner. Even then, you should
proceed with caution. These prototypes are not
for normal use - they are akin to base classes in a prototypal inheritance
system like JavaScript's. If you duplicate a prototype question the question
type will become unusable, as CodeRunner doesn't know which version of the
prototype to use.

### Preliminary testing of the CodeRunner question type

Once you have installed the CodeRunner question type, you should be able to
run CodeRunner questions using the University of Canterbury's Jobe Server
as a sandbox. It is
recommended that you do this before proceeding to install and configure your
own sandbox. Using the standard Moodle web interface, either as a Moodle
administrator or as a teacher in a course you have set up, go to the Question
Bank and try creating a new CodeRunner question. A simple Python3 test question
is: "Write a function *sqr(n)* that returns the square of its
parameter *n*.". Test cases for this might be:

<table>
<tr><th>Test</th><th>Expected</th></tr>
<tr><td>print(sqr(-7))</td><td>49</td></tr>
<tr><td>print(sqr(5))</td><td>25</td></tr>
<tr><td>print(sqr(-1))</td><td>1</td></tr>
<tr><td>print(sqr(0))</td><td>0</td></tr>
<tr><td>print(sqr(-100))</td><td>10000</td></tr>
</table>

You could check the 'UseAsExample' checkbox on the first two (which results
in the student seeing a simple "For example" table) and perhaps make the last
case a hidden test case. (It is recommended that all questions have at least
one hidden test case to prevent students synthesising code that works just for
the known test cases).

Save your new question, then preview it, entering both correct and
incorrect answers.

**IMPORTANT**: CodeRunner is designed to
work only in an Adaptive Mode so you must set the *How questions behave* dropdown
under *Attempt Options* to *Adaptive mode*. If you fail to do this, you'll
receive a message like "Detailed test results unavailable.
Perhaps an empty answer, or question behaviour not set to Adaptive Mode?". When setting
quizzes using CodeRunner questions, you should run the entire quiz in Adaptive
Mode, again using the 'Question behaviour' dropdown under Quiz Settings. If you
are using a Moodle server set up specifically to run CodeRunner questions, it is
recommended that you set the default question behaviour for the whole site to
Adaptive Mode. An Moodle administrator does this by going to
*Site administration > Plugins >
Activity Modules > Quiz* and selecting *Adaptive Mode* from the *How questions
behave* dropdown.

If you want a few more CodeRunner questions to play with, try importing the
files
`MoodleHome>/question/type/coderunner/db/simpledemoquestions.xml` and/or
`MoodleHome>/question/type/coderunner/db/python3demoquestions.xml`
These contains
all the questions from the two tutorial quizzes on the
[demo site](http://www.coderunner.org.nz). Note, though, that some of the
questions from the `python3demoquestions` file make use of the University of
Canterbury prototypes in `uoc_prototypes.xml`, so you'd need to import them, too.

### Sandbox Configuration

Although CodeRunner has a flexible architecture that supports various different
ways of running student task in a protected ("sandboxed") environment, only
one sandbox - the Jobe sandbox - is supported by the current version. This
sandbox makes use of a 
separate server, developed specifically for use by CodeRunner, called *Jobe*.
As explained
at the end of the section on installing CodeRunner from scratch, the initial
configuration uses the Jobe server at the University of Canterbury. This is not
suitable for production use. Please switch
to using your own Jobe server as soon as possible.

Follow the instructions at
[https://github.com/trampgeek/jobe](https://github.com/trampgeek/jobe)
to build a Jobe server, then use the
Moodle administrator interface for the CodeRunner plug-in to define the Jobe
host name and perhaps port number. Depending on how you've chosen to
configure your Jobe server, you may also need to supply an API-Key through
the same interface. If you intend running unit tests you
will also need to edit `tests/config.php` to set the correct URL for
the Jobe server.

Assuming you have built *Jobe* on a separate server, the JobeSandbox fully
isolates student code from the Moodle server. However, Jobe *can* be installed
on the Moodle server itself, rather than on a 
completely different machine. This works fine but is much less secure than running Jobe on
a completely separate machine. If a student program manages to break out of
the sandbox when it's running on a separate machine, the worst it can do is
bring the sandbox server down, whereas a security breach on the Moodle server
could be used to hack into the Moodle database, which contains student run results
and marks. That said, our Computer Science department used an earlier even less
secure Sandbox for some years without any ill effects; Moodle keeps extensive logs
of all activities, so a student deliberately breaching security is taking a
huge risk.

### Running the unit tests

If your Moodle installation includes the
*phpunit* system for testing Moodle modules, you might wish to test the
CodeRunner installation. Most tests require that at least python2 and python3
are installed.

Before running any tests you first need to edit the file
`<moodlehome>/question/type/coderunner/tests/config.php` to match
whatever configuration of sandboxes you wish to test and to set the jobe
server URL, if appropriate. You should then initialise
the phpunit environment with the commands

        cd <moodlehome>
        sudo php admin/tool/phpunit/cli/init.php

You can then run the full CodeRunner test suite with one of the following two commands,
depending on which version of phpunit you're using:

        sudo -u apache vendor/bin/phpunit --verbose --testsuite="qtype_coderunner test suite"

or

        sudo -u apache vendor/bin/phpunit --verbose --testsuite="qtype_coderunner_testsuite"

This will almost certainly show lots of skipped or failed tests relating
to the various sandboxes and languages that you have not installed, e.g.
the LiuSandbox,
Matlab, Octave and Java. These can all be ignored unless you plan to use
those capabilities. The name of the failing tests should be sufficient to
tell you if you need be at all worried.

Feel free to [email me](mailto:richard.lobb@canterbury.ac.nz) if you have problems
with the installation.

## The Architecture of CodeRunner

Although it's straightforward to write simple questions using the
built-in question types, anything more advanced than that requires
an understanding of how CodeRunner works.

The block diagram below shows the components of CodeRunner and the path taken
as a student submission is graded.

<img src="http://coderunner.org.nz/pluginfile.php/145/mod_page/content/2/coderunnerarchitecture.png" width="473" height="250" />

Following through the grading process step by step:

1. For each of the test cases, the [Twig template engine](http://twig.sensiolabs.org/) merges the student's submitted answer with
the question's per-test-case template together with code for this particular test case to yield an executable program.
By "executable", we mean a program that can be executed, possibly
with a preliminary compilation step.
1. The executable program is passed into whatever sandbox is configured
   for this question (e.g. the Jobe sandbox). The sandbox compiles the program (if necessary) and runs it,
   using the standard input supplied by the testcase.
1. The output from the run is passed into whatever Grader component is
   configured, as is the expected output specified for the test case. The most common grader is the
   "exact match" grader but other types are available.
1. The output from the grader is a "test result object" which contains
   (amongst other things) "Expected" and "Got" attributes.
1. The above steps are repeated for all testcases, giving an array of
   test result objects (not shown explicitly in the figure).
1. All the test results are passed to the CodeRunner question renderer,
   which presents them to the user as the Results Table. Tests that pass
   are shown with a green tick and failing ones shown with a red cross.
   Typically the whole table is coloured red if any tests fail or green
   if all tests pass.
       
The above description is somewhat simplified. Firstly, it 
ignores the existence of the "combinator template", which
combines all the test cases into a single executable
program. The per-test template is used only if there is no combinator
template or if each test case has its own standard input stream or if an
exception occurs during execution of the combined program.
This will all be explained later, in the section on templates. 

Secondly, there are several more-advanced features that are ignored by the
above, such as special customised grading templates, which generate an
executable program that does the grading of the student code as well.
A per-test-case template grader can be used to define each
row of the result table, or a combinator template grader can be used to
defines the entire result table. See the section on grading templates for
more information.

## Question types

CodeRunner support a wide variety of question types and can easily be
extended to support others. A CodeRunner question type is defined by a
*question prototype*, which specifies run time parameters like the execution
language and sandbox and also the templates that define how a test program is built from the
question's test-cases plus the student's submission. The prototype also
defines whether the correctness of the student's submission is assessed by use
of an *EqualityGrader*, a *NearEqualityGrader* or *RegexGrader*. The EqualityGrader expects
the output from the test execution to exactly match the expected output
for the testcase. The NearEqualityGrader is similar but is case insensitive
and tolerates variations in the amount of white space (e.g. missing or extra
blank lines, or multiple spaces where only one was expected).
The RegexGrader expects a regular expression match
instead. The EqualityGrader is recommended for all normal use as it
encourages students to get their output exactly correct; they should be able to
resubmit almost-right answers for a small penalty, which is generally a
better approach than trying to award part marks based on regular expression
matches.

Test cases are defined by the question author to check the student's code.
Each test case defines a fragment of test code, the standard input to be used
when the test program is run and the expected output from that run. The
author can also add additional files to the execution environment.

The test program is constructed from the test case information plus the
student's submission using one of two *templates* defined by the prototype.
The *per-test template* defines a different program for each test case.
To achieve higher efficiency with most
question types there is also a *combinator template* that defines a single
program containing *all* the different tests. If this template is defined,
and there is no standard input supplied,
CodeRunner
tries to use it first, but falls back to running the separate per-test-case
programs if any runtime exceptions occur. Templates are discussed in more
detail below.

### An example question type

The C-function question type expects students to submit a C function, plus possible
additional support functions, to some specification. For example, the question
might ask "Write a C function with signature `int sqr(int n)` that returns
the square of its parameter *n*". The author will then provide some test
cases of the form

        printf("%d\n", sqr(-11));

and give the expected output from this test. There is no standard input for
this question type. The per-test template wraps the student's
submission and
the test code into a single program like:

        #include <stdio.h>

        // --- Student's answer is inserted here ----

        int main()
        {
            printf("%d\n", sqr(-11));
            return 0;
        }

which is compiled and run for each test case. The output from the run is
then compared with
the specified expected output (121) and the test case is marked right
or wrong accordingly.

That example ignores the use of the combinator template, which in
the case of the built-in C function question type
builds a program with multiple `printf` calls interleaved with
printing of a special separator. The resulting output is then split
back into individual test case results using the separator string as a splitter.

### Built-in question types

The file `<moodlehome>/question/type/coderunner/db/builtin_PROTOTYPES.xml`
is a moodle-xml export format file containing the definitions of all the
built-in question types. During installation, and at the end of any version upgrade,
the prototype questions from that file are all loaded into a category
CR\_PROTOTYPES in the system context. A system administrator can edit
those prototypes but this is not generally recommended as the modified versions
will be lost on each upgrade. Instead, a category LOCAL\_PROTOTYPES
(or other such name of your choice) should be created and copies of any prototype
questions that need editing should be stored there, with the question-type
name modified accordingly. New prototype question types can also be created
in that category. Editing of prototypes is discussed later in this
document.

Built-in question types include the following:

 1. **c\_function**. This is the question type discussed in the above
example. The student supplies
 just a function (plus possible support functions) and each test is (typically) of the form

        printf(format_string, func(arg1, arg2, ..))

 The template for this question type generates some standard includes, followed
 by the student code followed by a main function that executes the tests one by
 one.

 The manner in which a C program is executed is not part of the question
 type definition: it is defined by the particular sandbox to which the
 execution is passed. The Jobe sandbox
 uses the gcc compiler with the language set to
 accept C99 and with both *-Wall* and *-Werror* options set on the command line
 to issue all warnings and reject the code if there are any warnings.


 1. **python3**. Used for most Python3 questions. For each test case, the student
code is run first, followed by the test code.

 1. **python3\_w\_input**. A variant of the *python3* question in which the
*input* function is redefined at the start of the program so that the standard
input characters that it consumes are echoed to standard output as they are
when typed on the keyboard during interactive testing. A slight downside of
this question type compared to the *python3* type is that the student code
is displaced downwards in the file so that line numbers present in any
syntax or runtime error messages do not match those in the student's original
code.

 1. **python2**. Used for most Python2 questions. As for python3, the student
code is run first, followed by the sequence of tests. This question type
should be considered to be
obsolescent due to the widespread move to Python3 through the education
community.

 1. **java\_method**. This is intended for early Java teaching where students are
still learning to write individual methods. The student code is a single method,
plus possible support methods, that is wrapped in a class together with a
static main method containing the supplied tests (which will generally call the
student's method and print the results).

 1. **java\_class**. Here the student writes an entire class (or possibly
multiple classes in a single file). The test cases are then wrapped in the main
method for a separate
public test class which is added to the students class and the whole is then
executed. The class the student writes may be either private or public; the
template replaces any occurrences of `public class` in the submission with
just `class`. While students might construct programs
that will not be correctly processed by this simplistic substitution, the
outcome will simply be that they fail the tests. They will soon learn to write
their
classes in the expected manner (i.e. with `public` and `class` on the same
line, separated by a single space)!

 1. **java\_program**. Here the student writes a complete program which is compiled
then executed once for each test case to see if it generates the expected output
for that test. The name of the main class, which is needed for naming the
source file, is extracted from the submission by a regular expression search for
a public class with a `public static void main` method.

 1. **octave\_function**. This uses the open-source Octave system to process
matlab-like student submissions.

 1. **php**. A php question in which the student submission is a normal php
file, with PHP code enclosed in <?php ... ?> tags and the output is the
usual PHP output including all HTML content outside the php tags.

As discussed later, this base set of question types can
be customised or extended in various ways.

C++ isn't available as a built-in type at present, as we don't teach it.
However, as the Jobe server is by default configured to run C++ jobs (using
the language ID 'cpp') you can easily make a custom C++ question
type by starting with the C question type, setting the language to *cpp*
and changing the template to include
*iostream* instead of, or as well as, *stdio.h*. The line

        using namespace std;

 may also be desirable.

### Some more-specialised question types

The following question types used to exist as built-ins but have now been
dropped from the main install as they are intended primarily for University
of Canterbury (UOC) use only. They can be imported, if desired, from the file
**uoc_prototypes.xml**, located in the CodeRunner/coderunner/db folder.

The UOC question types include:

 1. **python3\_cosc121**. This is a complex Python3 question
type that's used at the University of Canterbury for nearly all questions in
the COSC121 course.  The student submission
is first passed through the [pylint](http://www.logilab.org/857)
source code analyser and the submission is rejected if pylint gives any errors.
Otherwise testing proceeds as normal. Obviously, pylint needs to be installed
on the sandbox server. This question type takes the following template
parameters (see the section entitled *Template parameters* for an explanation
of what these are) to allow it to be used for a wide range of different problems:

    * isfunction: unless this is explicitly set to false, a dummy module docstring
will be inserted at the start of the program unless there is one there already.
Thus, if your question is of the "write a program" variety, you should set this
to false. Otherwise omit it. This purpose is to stop pylint issuing a spurious
missing module docstring message.

    * pylintoptions: this should be a JSON list of strings. For example,
the Template parameters string in the question authoring form might be set to
{"isfunction": false, "pylintoptions":["--max-statements=20","--max-args=3"]}
to suppress the insertion of a dummy module docstring at the start and to set
the maximum number of statements and arguments for each function to 20 and 3
respectively. See the pylint documentation for a list of its options.

    * proscribedconstructs: this is a list of Python constructs
(if, while, def, etc) that must not appear in the student's program.

    * requiredconstructs: this is a list of Python constructs
(if, while, def, etc) that must appear in the student's program.

    * allowglobals: set this to true to allow global variables (i.e. to
allow lowercase globals, not just ALL_CAPS "constants")

    * maxnumconstants: the maximum number of constants (i.e. uppercase globals)
allowed. An integer, defaulting to 4. Some such constraint is required when
teaching pylint at early stages to stop students achieving pylint compliance
with a global script simply by typing all identifiers in upper case.

    * norun: if set to true, the normal execution of the student's code will
not take place. Any test code provided will however still be run. This is
intended for dummy questions that allow students to check if their code is
pylint-compliant.

    * stripmain: if set to True, the program is expected to contain a
global invocation of the main function, which is a line starting "main()".
All such calls to main are replaced by 'pass'. If no such line is not present
a "Missing call to main" exception is raised. Stripping the main gives the
question author the ability to test individual functions in the student submission
as well as, or instead of, testing the program as a whole by explicitly
calling *main()*.

    * runextra: if set (to any value) the Extra Template Data is added to the
program as test code before the usual testcode. This allows the question
author to load extra test code through the Extra Template Data which the
student does not get to see (usually because it would confuse them).


 1. **matlab\_function**. Used for Matlab function questions. Student code must be a
function declaration, which is tested with each testcase. The name is actually
a lie, as this question type now uses Octave instead, which is much more
efficient and easier for the question author to program within the CodeRunner
context. However, Octave has many subtle differences
from Matlab and some problems are inevitable. Caveat emptor.

 1. **matlab\_script**. Like matlab\_function, this is a lie as it actually
uses Octave. It runs the test code first (which usually sets up a context)
and then runs the student's code, which may or may not generate output
dependent on the context. Finally the code in Extra Template Data is run
(if any). Octave's `disp` function is replaced with one that emulates 
Matlab's more closely, but, as above: caveat emptor.

 1. **nodejs**. A question type in which the student's JavaScript submission
is followed by the test code and the whole program is executed using *nodejs*.

## Templates

Templates are the key to understanding how a submission is tested. There are in
general two templates per question type (i.e. per prototype) - a *combinator\_template* and a
*per\_test\_template*. We'll discuss the latter for a start.

The *per\_test\_template* for each question type defines how a program is built from the
student's code and one particular testcase. That program is compiled (if necessary)
and run with the standard input defined in that testcase, and the output must
then match the expected output for the testcase (where 'match' is defined
by the chosen validator: an exact match, a nearly exact match or a
regular-expression match.

The question type template is processed by the
[Twig](http://twig.sensiolabs.org/) template engine. The engine is given both
the template and a variable called
STUDENT\_ANSWER, which is the text that the student entered into the answer box,
plus another called TEST, which is a record containing the test-case
that the question author has specified
for the particular test. The TEST attributes most likely to be used within
the template are TEST.testcode (the code to execute for the test), TEST.stdin
(the standard input for the test -- not normally used within a template, but
occasionally useful), TEST.extra (the extra template data provided in the
question authoring form). The template will typically use just the TEST.testcode
field, which is the "test" field of the testcase, and usually (but not always)
is a bit of code to be run to test the student's answer. As an example,
the question type *c\_function*, which asks students to write a C function,
has the following template:

        #include <stdio.h>
        #include <stdlib.h>
        #include <ctype.h>

        {{ STUDENT_ANSWER }}

        int main() {
            {{ TEST.testcode }};
            return 0;
        }

A typical test (i.e. `TEST.testcode`) for a question asking students to write a
function that
returns the square of its parameter might be:

        printf("%d\n", sqr(-9))

with the expected output of 81. The result of substituting both the student
code and the test code into the template would then be a program like:

        #include <stdio.h>
        #include <stdlib.h>
        #include <ctype.h>

        int sqr(int n) {
            return n * n;
        }

        int main() {
            printf("%d\n", sqr(-9));
            return 0;
        }

When authoring a question you can inspect the template for your chosen
question type by temporarily checking the 'Customise' checkbox. Additionally,
if you check the *Template debugging* checkbox you will get to see
in the output web page each of the
complete programs that gets run during a question submission.

As mentioned earlier, there are actually two templates for each question
type. For efficiency, CodeRunner first tries
to combine all testcases into a single compile-and-execute run using the second
template, called the `combinator_template`. There is a combinator
template for most
question types, except for questions that require students
to write a whole program. However, the combinator template is not used during
testing if standard input is supplied for any of the tests; each test
is then assumed to be independent of the others, with its own input. Also,
if an exception occurs at runtime when a combinator template is being used,
the tester retries all test cases individually using the per-test-case
template so that the student gets presented with all results up to the point
at which the exception occurred.

As mentioned above, both the `per_test_template` and the `combinator_template`
can be edited by the question
author for special needs, e.g. if you wish to provide skeleton code to the
students. As a simple example, if you wanted students to provide the missing
line in a C function that returns the square of its parameter, and you
also wished to hide the *printf* from the students, you could use
a template like:

        #include <stdio.h>
        #include <stdlib.h>
        #include <ctype.h>

        int sqr(int n) {
           {{ STUDENT_ANSWER }}
        }

        int main() {
            printf("%d\n", {{ TEST.testcode }});
            return 0;
        }

The testcode would then just be of the form `sqr(-11)`, and the question text
would need to make it clear
to students what context their code appears in. The authoring interface
allows the author to set the size of the student's answer box, and in a
case like the above you'd typically set it to just one or two lines in height
and perhaps 30 columns in width.

You will need to understand loops and selection in
the Twig template engine if you wish to write your own combinator templates.
For one-off question use, the combinator template doesn't normally offer
sufficient additional benefit to warrant the complexity increase
unless you have a
large number of testcases or are using
a slow-to-launch language like Matlab. However, if you are writing your
own question prototypes you might wish to make use of it.

## Advanced template use

It may not be obvious from the above that the template mechanism allows
for almost any sort of question where the answer can be evaluated by a computer.
In all the examples given so far, the student's code is executed as part of
the test process but in fact there's no need for this to happen. The student's
answer can be treated as data by the template code, which can then execute
various tests on that data to determine its correctness. The Python *pylint*
question type mentioned earlier is a simple example: the template code first
writes the student's code to a file and runs *pylint* on that file before
proceeding with any tests.

The per-test template for such a question type in its
simplest form might be:

    import subprocess
    import os
    import sys

    def code_ok(prog_to_test):
        """Check prog_to_test with pylint. Return True if OK or False if not.
           Any output from the pylint check will be displayed by CodeRunner
        """
        try:
            source = open('source.py', 'w')
            source.write(prog_to_test)
            source.close()
            env = os.environ.copy()
            env['HOME'] = os.getcwd()
            cmd = ['pylint', 'source.py']
            result = subprocess.check_output(cmd, 
                universal_newlines=True, stderr=subprocess.STDOUT, env=env)
        except Exception as e:
            result = e.output

        if result.strip():
            print("pylint doesn't approve of your program", file=sys.stderr)
            print(result, file=sys.stderr)
            print("Submission rejected", file=sys.stderr)
            return False
        else:
            return True


    __student_answer__ = """{{ STUDENT_ANSWER | e('py') }}"""
    if code_ok(__student_answer__):
        __student_answer__ += '\n' + """{{ TEST.testcode | e('py') }}"""
        exec(__student_answer__)

The Twig syntax {{ STUDENT\_ANSWER | e('py') }} results in the student's submission
being filtered by a Python escape function that escapes all
double quote and backslash characters with an added backslash.

Note that any output written to *stderr* is interpreted by CodeRunner as a
runtime error, which aborts the test sequence, so the student sees the error
output only on the first test case.

The full `Python3_pylint` question type is a bit more complex than the
above. It is given in full in the section on *template parameters*.

Some other more complex examples that we've used include:

 1. A Matlab question in which the template code (also Matlab) breaks down
    the student's code into functions, checking the length of each to make
    sure it's not too long, before proceeding with marking.

 1. A Python question where the student's code is actually a compiler for
    a simple language. The template code runs the student's compiler,
    passes its output through an assembler that generates a JVM class file,
    then runs that class with the JVM to check its correctness.

 1. A Python question where the students submission isn't code at all, but
    is a textual description of a Finite State Automaton for a given transition
    diagram; the template code evaluates the correctness of the supplied
    automaton.


### Twig Escapers

As explained above, the Twig syntax {{ STUDENT\_ANSWER | e('py') }} results
in the student's submission
being filtered by a Python escape function that escapes all
all double quote and backslash characters with an added backslash. The
python escaper e('py') is just one of the available escapers. Others are:

 1. e('java'). This prefixes single and double quote characters with a backslash
    and replaces newlines, returns, formfeeds, backspaces and tabs with their
    usual escaped form (\n, \r etc).

 1. e('c').  This is an alias for e('java').

 1. e('matlab'). This escapes single quotes, percents and newline characters.
    It must be used in the context of Matlab's sprintf, e.g.

        student_answer = sprintf('{{ STUDENT_ANSWER | e('matlab')}}');

 1. e('js'), e('html') for use in JavaScript and html respectively. These
    are Twig built-ins. See the Twig documentation for details.

## Template parameters

It is sometimes necessary to make quite small changes to a template over many
different questions. For example, you might want to use the *pylint* question
type given above but change the maximum allowable length of a function in different
questions. Customising the template for each such question has the disadvantage
that your derived questions no longer inherit from the original prototype, so
that if you wish to alter the prototype you will also need to find
and modify all the
derived questions, too.

In such cases a better approach may be to use template parameters.

If the *+Show more* link on the CodeRunner question type panel in the question
authoring form is clicked, some extra controls appear. One of these is
*Template parameters*. This can be set to a JSON-encoded record containing
definitions of variables that can be used by the template engine to perform
local per-question customisation of the template. The template parameters
are passed to the template engine as the object `QUESTION.parameters`.

A more complete version of the Python3_pylint question type, which allows
customisation of the pylint options via template parameters and also allows
for an optional insertion of a module docstring for "write a function"
questions is then:

    import subprocess
    import os
    import sys

    def code_ok(prog_to_test):
    {% if QUESTION.parameters.isfunction %}
        prog_to_test = "'''Dummy module docstring'''\n" + prog_to_test
    {% endif %}
        try:
            source = open('source.py', 'w')
            source.write(prog_to_test)
            source.close()
            env = os.environ.copy()
            env['HOME'] = os.getcwd()
            pylint_opts = []
    {% for option in QUESTION.parameters.pylintoptions %}
            pylint_opts.append('{{option}}')
    {% endfor %}
            cmd = ['pylint', 'source.py'] + pylint_opts
            result = subprocess.check_output(cmd, 
                universal_newlines=True, stderr=subprocess.STDOUT, env=env)
        except Exception as e:
            result = e.output

        if result.strip():
            print("pylint doesn't approve of your program", file=sys.stderr)
            print(result, file=sys.stderr)
            print("Submission rejected", file=sys.stderr)
            return False
        else:
            return True


    __student_answer__ = """{{ STUDENT_ANSWER | e('py') }}"""
    if code_ok(__student_answer__):
        __student_answer__ += '\n' + """{{ TEST.testcode | e('py') }}"""
        exec(__student_answer__)

The `{% if` and 
`{% for` are Twig control structures that conditionally insert extra data
from the template parameters field of the author editing panel.

### The Twig QUESTION variable
As may be deduced from the previous section, there is a Twig template variable
called `QUESTION`, which is an object containing all the fields of the
PHP question object. Some of the other
QUESTION fields/attributes that might be of interest to authors include the
following.

 * QUESTION.questionid The unique internal ID of this question. 
 * QUESTION.questiontext The question text itself
 * QUESTION.answer The supplied sample answer (null if not explicitly set).
 * QUESTION.language The language being used to run the question in the sandbox,
e.g. "Python3".
 * QUESTION.useace '1'/'0' if the ace editor is/is not in use.
 * QUESTION.sandbox The sandbox being used, e.g. "jobesandbox".
 * QUESTION.grader The PHP grader class being used, e.g. "EqualityGrader".
 * QUESTION.cputimelimitssecs The allowed CPU time (null unless explicitly set).
 * QUESTION.memlimitmb The allowed memory in MB (null unless explicitly set).
 * QUESTION.sandboxparams The JSON string used to specify the sandbox parameters
in the question authoring form (null unless explicitly set).
 * QUESTION.templateparams The JSON string used to specify the template
parameters in the question authoring form. (Normally the question author
will not use this but will instead access the specific parameters as in
the previous section).
 * QUESTION.resultcolumns The JSON string used in the question authoring
form to select which columns to display, and how to display them (null
unless explicitly set).


## Grading with templates
Using just the template mechanism described above it is possible to write
almost arbitrarily complex questions. Grading of student submissions can,
however, be problematic in some situations. For example, you may need to
ask a question where many different valid program outputs are possible, and the
correctness can only be assessed by a special testing program. Or
you may wish to subject
a student's code to a very large
number of tests and award a mark according to how many of the test cases
it can handle. The usual exact-match
grader cannot handle these situations. For such cases one of the two
template grading options can be used.

### Per-test-case template grading

When the 'Per-test-case template grader' is selected as the grader
the per-test-case template
changes its role to that of a grader for a particular test case.
The combinator template is not used
and the per-test-case template is applied to each test case in turn. The
output of the run is not passed to the grader but is taken as the
grading result for the corresponding row of the result table.
The output from the template-generated program must now
be a JSON-encoded object (such as a dictionary, in Python) containing
at least a 'fraction' field, which is multiplied by TEST.mark to decide how
many marks the test case is awarded. It should usually also contain a 'got'
field, which is the value displayed in the 'Got' column of the results table.
The other columns of the results table (testcode, stdin, expected) can also
be defined by the custom grader and will be used instead of the values from
the test case. As an example, if the output of the program is the string

    {"fraction":0.5, "got": "Half the answers were right!"}

half marks would be
given for that particular test case and the 'Got' column would display the
text "Half the answers were right!".

For even more flexibility the *result_columns* field in the question editing
form can be used to customise the display of the test case in the result
table. That field allows the author to define an arbitrary number of arbitrarily
named result-table columns and to specify using *printf* style formatting
how the attributes of the grading output object should be formatted into those
columns. For more details see the section on result-table customisation.

Writing a grading template that executes the student's code is, however,
rather difficult as the generated program needs to be robust against errors
in the submitted code. The template-grader should always return a JSON object
and should not generate any stderr output.

Sometimes the author of a template grader wishes to abort the testing of the 
program after a test case, usually the first, e.g. when pre-checks on the
acceptability of a student submission fail. This can be achieved by defining
in the output JSON object an extra attribute `abort`, giving it the boolean
value `true`. If such
an attribute is defined, any supplied `fraction` value will be ignored, the
test case will be marked wrong (equivalent to `fraction = 0`) and all further
test cases will be skipped. For example:

`{"fraction":0.0, "got":"Invalid submission!", "abort":true}`

Note to Python programmers: the Python literal is `True` rather than `true`,
so if generating JSON with `json.dumps()`, you need to write

`json.dumps({"fraction":0.0, "got":"Invalid submission!", "abort":True})`

### Combinator-template grading

The ultimate in grading flexibility is achieved by use of the "Combinator
template grading" option. In this mode the per-test template is not used. The
combinator template is passed to the Twig template engine and the output
program is executed in the usual way. Its output must now be a JSON-encoded
object with two mandatory attributes: a *fraction* in the range 0 - 1,
which specifies the fractional mark awarded to the question, and a
*feedbackhtml* that fully defines the specific feedback to be presented
to the student in place of the normal results table. It might still be a
table, but any other HTML-supported output is possible such as paragraphs of
text, canvases or SVG graphics. The *result_columns* field from the
question editing form is ignored in this mode.

Combinator-template grading is intended for use where a result table is just not
appropriate, e.g. if the question does not involve programming at
all. As an extreme example, imagine a question that asks the student to
submit an English essay on some topic and an AI grading program is used
to mark and to generate
a report on the quality of the essay for feedback to the student.
[Would that such AI existed!] 

The combinator-template grader has available to it the full list of all
test cases and their attributes (testcode, stdin, expected, mark, display etc)
for use in any way the question author sees fit. It is highly likely that
many of them will be disregarded or alternatively have some meaning completely
unlike their normal meaning in a programming environment. It is also
possible that a question using a combinator template grader will not
make use of test cases at all.

## A simple grading-template example
A simple case in which one might use a template grader is where the
answer supplied by the student isn't actually code to be run, but is some
sort of raw text to be graded by computer. For example,
the student's answer might be the output of some simulation the student has
run. To simplify further, let's assume that the student's answer is
expected to be exactly 5 lines of text, which are to be compared with
the expected 5 lines, entered as the 'Expected' field of a single test case.
One mark is to be awarded for each correct line, and the displayed output
should show how each line has been marked (right or wrong).

A template grader for this situation might be the following

        import json

        got = """{{ STUDENT_ANSWER | e('py') }}"""
        expected = """{{ TEST.expected | e('py') }}"""
        got_lines = got.split('\n')
        expected_lines = expected.split('\n')
        mark = 0
        if len(got_lines) != 5:
            comment = "Expected 5 lines, got {}".format(len(got_lines))
        else:
            comment = ''
            for i in range(5):
                if got_lines[i] == expected_lines[i]:
                    mark += 1
                    comment += "Line {} right\n".format(i)
                else:
                    comment += "Line {} wrong\n".format(i)

        print(json.dumps({'got': got, 'comment': comment, 'fraction': mark / 5}))

In order to display the *comment* in the output JSON, the 
the 'Result columns' field of the question (in the 'customisation' part of
the question authoring form) should include that field and its column header, e.g.

        [["Expected", "expected"], ["Got", "got"], ["Comment", "comment"], ["Mark", "awarded"]]

The following two images show the student's result table after submitting
a fully correct answer and a partially correct answer, respectively.

![right answer](http://coderunner.org.nz/pluginfile.php/56/mod_page/content/15/Selection_052.png)

![partially right answer](http://coderunner.org.nz/pluginfile.php/56/mod_page/content/15/Selection_053.png)

## A more advanced grading-template example
A template-grader can also be used to grade programming questions when the
usual graders (e.g. exact or regular-expression matching of the program's
output) are inadequate. 

As a simple example, suppose the student has to write their own Python square
root function (perhaps as an exercise in Newton Raphson iteration?), such
that their answer, when squared, is within an absolute tolerance of 0.00001
of the correct answer. To prevent them from using the math module, any use
of an import statement would need to be disallowed but we'll ignore that aspect
in order to focus on the grading aspect.

The simplest way to deal with this issue is to write a series of testcases
of the form

        approx = student_sqrt(2)
        right_answer = math.sqrt(2)
        if math.abs(approx - right_answer) < 0.00001:
            print("OK")
        else:
            print("Fail (got {}, expected {})".format(approx, right_answer))

where the expected output is "OK". However, if one wishes to test the student's
code with a large number of values - say 100 or more - this approach becomes
impracticable. For that, we need to right our own tester, which we can do
using a template grade.

Template graders that run student-supplied code are somewhat tricky to write
correctly, as they need to output a valid JSON record under all situations,
handling problems like extraneous output from the student's code, runtime
errors or syntax error. The safest approach is usually to run the student's
code in a subprocess and then grade the output.

A per-test template grader for the student square root question, which tests
the student's *student_sqrt* function with 100 random numbers in the range
0 to 1000, might be as follows:

        import subprocess, json, sys
        student_func = """{{ STUDENT_ANSWER | e('py') }}"""

        if 'import' in student_func:
            output = 'The word "import" was found in your code!'
            result = {'got': output, 'fraction': 0}
            print(json.dumps(result))
            sys.exit(0)

        test_program = """import math
        from random import uniform
        TOLERANCE = 0.000001
        NUM_TESTS = 1000
        {{ STUDENT_ANSWER | e('py') }}
        ok = True
        for i in range(NUM_TESTS):
            x = uniform(0, 1000)
            stud_answer = student_sqrt(n)
            right = math.sqrt(x)
            if abs(right - stud_answer) > TOLERANCE:
                print("Wrong sqrt for {}. Expected {}, got {}".format(x, right, stud_answer))
                ok = False
                break

        if ok:
            print("All good!")
        """
        try:
            with open('code.py', 'w') as fout:
                fout.write(test_program)
            output = subprocess.check_output(['python3', 'code.py'], 
                stderr=subprocess.STDOUT, universal_newlines=True)
        except subprocess.CalledProcessError as e:
            output = e.output

        mark = 1 if output.strip() == 'All good!' else 0
        result = {'got': output, 'fraction': mark}
        print(json.dumps(result))
        
The following figures show this question in action.

![right answer](http://coderunner.org.nz/pluginfile.php/56/mod_page/content/23/Selection_061.png)
![Insufficient iterations](http://coderunner.org.nz/pluginfile.php/56/mod_page/content/23/Selection_060.png)
![Syntax error](http://coderunner.org.nz/pluginfile.php/56/mod_page/content/23/Selection_062.png)


Obviously, writing questions using custom graders is much harder than
using the normal built-in equality based grader. It is usually possible to
ask the question in a different way that avoids the need for a custom grader.
In the above example, you would have to ask yourself if it mightn't have been
sufficient to test the function with 10 fixed numbers in the range 0 to 1000
using ten different test cases of the type suggested in the third 
paragraph of this section.

## Customising the result table

The output from the standard graders is a list of so-called *TestResult* objects,
each with the following fields (which include the actual test case data):

    testcode      // The test that was run (trimmed, snipped)
    iscorrect     // True iff test passed fully (100%)
    expected      // Expected output (trimmed, snipped)
    mark          // The max mark awardable for this test
    awarded       // The mark actually awarded.
    got           // What the student's code gave (trimmed, snipped)
    stdin         // The standard input data (trimmed, snipped)
    extra         // Extra data for use by some templates


A field called *result_columns* in the question authoring form can be used
to control which of these fields are used, how the columns are headed and
how the data from the field is formatted into the result table.

By default the result table displays
the testcode, stdin, expected and got columns, provided the columns
are not empty. Empty columns are dropped from the table.
You can change the default, and/or the column headers
by entering a value for *result_columns* (leave blank for the default
behaviour). If supplied, the result_columns field must be a JSON-encoded
list of column specifiers.

### Column specifiers

Each column specifier is itself a list,
typically with just two or three elements. The first element is the
column header, the second element is usually the field from the TestResult
object being displayed in the column (one of those values listed above) and the optional third
element is an sprintf format string used to display the field.
Custom-grader templates may add their
own fields, which can also be selected for display. It is also possible
to combine multiple fields into a column by adding extra fields to the
specifier: these must precede the sprintf format specifier, which then
becomes mandatory. For example, to display a Mark Fraction column in the
form `0.74 out of 1.00`, a column format specifier of `["Mark Fraction", "awarded",
"mark", "%.2f out of %.2f"]` could be used. 

### HTML formatted columns

As a special case, a format
of `%h` means that the test result field should be taken as ready-to-output
HTML and should not be subject to further processing; this can be useful
with custom-grader templates that generate HTML output, such as
SVG graphics, and we have also used it in questions where the output from
the student's program was HTML. A third use is with the diff filter, explained
in the following section.

NOTE: `%h` format required PHP >= 5.4.0 and Libxml >= 2.7.8 in order to
parse and clean the HTML output.

### Extended column specifier syntax

It was stated above that the values to be formatted by the format string (if
given) were fields from the TestResult object. This is a slight simplification. 
The syntax actually allows for expressions of the form:

        filter(testResultField [,testResultField]... )

where `filter` is the name of a built-in filter function that filters the
given testResult field(s) in some way. Currently the only such built-in
filter function is `diff`: it takes two test result fields as parameters and
returns an HTML string that represents the first test field with embedded
HTML &lt;ins&gt; and <&lt;del&gt; elements that show the insertions and deletions necessary
to convert the first field into the second. This is intended for use with
the `Expected` and `Got` fields, so the student can easily see where their
output is wrong. It is used by the default result column display if an
ExactMatch grader is used (see below).

If the diff filter is being used, the column specifier should have exactly
three elements: the column header, the diff expression and a format specifier
of '%h'.

The stylesheet by default displays &lt;del&gt; content in the same style as the
surrounding text and hides the &lt;ins&gt; content so that the first field is
displayed verbatim. If the question does not earn full marks, a "Show differences"
button is added at the end of the results table. When this is clicked, any
&lt;del&gt; content is highlighted. &lt;ins&gt; content remains hidden. With the default
settings (see below) the highlighting in the *Expected* column thus shows
what is missing in the *Got* column, while the highlighting in the *Got* column
shows additional text that shouldn't be present. 

Other filter functions may be added in the future if demand arises.

### Default result columns

The default value of *result_columns* for questions using an ExactMatch grader
is

`[["Test", "testcode"],
["Input", "stdin"], ["Expected", "diff(expected, got)", "%h"],
["Got", "diff(got, expected)", "%h"]]`.

Otherwise the default value is:

`[["Test", "testcode"], ["Input", "stdin"], ["Expected", "expected"], ["Got", "got"]]`.



## User-defined question types

NOTE: User-defined question types are very powerful but are not for the faint
of heart. There are some known pitfalls, so please read the following very
carefully.

As explained earlier, each question type is defined by a prototype question,
which is just
another question in the database from which new questions can inherit. When
customising a question, if you open the *Advanced customisation* panel you'll
find the option to save your current question as a prototype. You will have
to enter a name for the new question type you're creating. It is strongly
recommended that you also change the name of your question to reflect the
fact that it's a prototype, in order to make it easier to find. The convention
is to start the question name with
the string PROTOTYPE\_, followed by the type name. For example,
PROTOTYPE\_python3\_OOP. Having a
separate PROTOTYPES category for prototype questions is also strongly recommended.
Obviously the
question type name you use should be unique, at least within the context of the course
in which the prototype question is being used.

The question text of a prototype question is displayed in the 'Question type
details' panel in the question authoring form. 

CodeRunner searches for prototype questions
just in the current course context. The search includes parent
contexts, typically visible only to an administrator, such as the system
context; the built-in prototypes all reside in that system context. Thus if
a teacher in one course creates a new question type, it will immediately
appear in the question type list for all authors editing questions within
that course but it will not be visible to authors in other courses. If you wish
to make a new question type available globally you should ask a
Moodle administrator
to move the question to the system context, such as a LOCAL\_PROTOTYPES
category.

When you create a question of a particular type, including user-defined
types, all the so-called "customisable" fields are inherited from the
prototype. This means changes to the prototype will affect all the "children"
questions. **However, as soon as you customise a child question you copy all the
prototype fields and lose that inheritance.**

To reduce the UI confusion, customisable fields are subdivided into the
basic ones (per-test-template, grader, result-table column selectors etc) and
"advanced"
ones. The latter include the language, sandbox, timeout, memory limit and
the "make this question a prototype" feature. The combinator
template is also considered to be an advanced feature.

**WARNING #1:** if you define your own question type you'd better make sure
when you export your question bank
that you include the prototype, or all of its children will die on being imported
anywhere else! 
Similarly, if you delete a prototype question that's actually
in use, all the children will break, giving runtime errors. To recover
from such screw ups you will need to create a new prototype
of the right name (preferably by importing the original correct prototype).
To repeat:
user-defined question types are not for the faint of heart. Caveat emptor.

**WARNING #2:** although you can define test cases in a question prototype
these have no relevance and are silently ignored.

## APPENDIX: How programming quizzes should work

Historical notes and a diatribe on the use of Adaptive Mode questions ...

The original pycode was inspired by [CodingBat](http://codingbat.com), a site where
students submit Python or Java code that implements a simple function or
method, e.g. a function that returns twice the square of its parameter plus 1.
The student code is executed with a series of tests cases and results are
displayed immediately after submission in a simple tabular form showing each
test case, expected answer
and actual answer. Rows where the answer computed by the student's code
is correct receive a large green tick; incorrect rows
receive a large red cross. The code is deemed correct only if all tests
are ticked. If code is incorrect, students can simply correct it and resubmit.

*CodingBat* proves extraordinarily effective as a student training site. Even
experienced programmers receive pleasure from the column of green ticks and
all students are highly motivated to fix their code and retry if it fails one or more
tests. Some key attributes of this success, to be incorporated into *pycode*,
were:

1. Instant feedback. The student pastes their code into the site, clicks
*submit*, and almost immediately receives back their results.

1. All-or-nothing correctness. If the student's code fails any test, it is
wrong. Essentially (thinking in a quiz context) it earns zero marks. Code
has to pass *all* tests to be deemed mark-worthy.

1. Simplicity. The question statement should be simple. The solution should
also be reasonably simple. The display of results is simple and the student
knows immediately what test cases failed. There are no complex regular-expression
failures for the students to puzzle over nor uncertainties over what the
test data was.

1. Rewarding green ticks. As noted above, the colour and display of a correct
results table is highly satisfying and a strong motivation to succeed.

The first two of these requirements are particularly critical. While they can
be accommodated within Moodle by using an *adaptive* quiz behaviour
in conjunction with an all-or-nothing marking scheme, they are not
how many people view a Moodle quiz. Quizzes are
commonly marked only after submission of all questions, and there is usually
a perception that part marks will be awarded for "partially correct" answers.
However, awarding marks to a piece of code according to how many test cases
it passes can give almost meaningless results. For example, a function that
always just returns 0, or the empty list or equivalent, will usually pass several
of the tests, but surely it shouldn't be given *any* marks? Seriously flawed
code, for example a string tokenizing function that works only with alphabetic
data, may get well over half marks if the question-setter was not expecting
such flaws.

Accordingly, a key assumption underlying CodeRunner is that quizzes will always
run in Moodle's adaptive mode, which displays results after each question
is submitted, and allows resubmission for a penalty. The mark obtained in a
programming-style quiz is thus determined by how many of the problems the
student can solve in the given time, and how many submissions the student
needs to make on each question.
