import os
import subprocess
import sys
import argparse
from datetime import datetime

def write_to_file(path, content):
    with open(path, "a", encoding="utf-8") as f:
        f.write(content + "\n")

def run_codex(prompt):
    try:
        result = subprocess.run(
            ["codex", prompt],
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True
        )
        return result.stdout.strip(), result.stderr.strip()
    except Exception as e:
        return "", f"Erreur d'exécution : {e}"

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("workdir", help="Nom du dossier de travail (ex: ecommerce)")
    args = parser.parse_args()

    workdir_path = os.path.abspath(args.workdir)
    parent_dir = os.getcwd()

    timestamp = datetime.now().strftime("%Y%m%d-%H%M%S")
    histo_file = os.path.join(parent_dir, f"codex_session_history_{timestamp}.txt")
    error_file = os.path.join(parent_dir, f"codex_error_log_{timestamp}.txt")

    if not os.path.isdir(workdir_path):
        print(f"Erreur : le dossier {workdir_path} n'existe pas.")
        sys.exit(1)

    os.chdir(workdir_path)
    print(f"Changement de dossier vers : {workdir_path}")
    print("Session codex (commande par commande). Tape 'exit' pour quitter.\n")

    while True:
        try:
            cmd = input(">>> ")
            if cmd.strip().lower() == "exit":
                print("Fin de session.")
                break

            write_to_file(histo_file, "[COMMAND] " + cmd)
            out, err = run_codex(cmd)

            if out:
                print(out)
                write_to_file(histo_file, "[RESPONSE] " + out)
            if err:
                print("[stderr]", err)
                write_to_file(error_file, err)

        except KeyboardInterrupt:
            print("\nInterruption.")
            break
        except Exception as e:
            write_to_file(error_file, str(e))
            print(f"Erreur : {e}")

if __name__ == "__main__":
    main()
